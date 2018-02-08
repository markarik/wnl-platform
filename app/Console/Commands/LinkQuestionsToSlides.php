<?php namespace App\Console\Commands;

use Storage;
use Artisan;
use App\Models\QuizQuestion;
use App\Models\Slide;
use Lib\SlideParser\Parser;
use Illuminate\Console\Command;

class LinkQuestionsToSlides extends Command
{
	const BASE_DIRECTORY = 'slideshows';

	protected $parser;

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'slides:linkQuestions {dir?}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import slideshows to database from storage.';

	/**
	 * Create a new command instance.
	 *
	 * @param Parser $parser
	 */
	public function __construct(Parser $parser)
	{
		parent::__construct();

		$this->parser = $parser;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$path = self::BASE_DIRECTORY;

		if ($subDir = $this->argument('dir')) {
			$path .= '/' . $subDir;
		}

		$files = Storage::disk('s3')->files($path);
		$this->info('Importing slideshows...');
		$bar = $this->output->createProgressBar(count($files));

		foreach ($files as $file) {
			$this->linkSlidesFromFile($file);
			$bar->advance();
			\Log::debug($file . ' processed');
		}
		if (!$files) $this->linkSlidesFromFile($path);
		print PHP_EOL;

		Artisan::queue('cache:tag', ['tag' => 'api']);

		return;
	}

	/**
	 * Import slideshow form file.
	 *
	 * @param $file
	 */
	public function linkSlidesFromFile($file, $screenId = null)
	{
		$this->info("Parsing {$file}");

		$contents = Storage::disk('s3')->get($file);
		$slides = $this->parser->matchSlides($contents);
		$slidesCount = count($slides);

		$this->info("Found {$slidesCount} slides.");

		$bar = $this->output->createProgressBar($slidesCount);

		foreach ($slides as $index => $slide) {
			$cleanSlide = $this->parser->cleanSlide($slide);
			$tags = $this->parser->getTags($slide);

			foreach ($tags as $tagName => $tagValues) {
				if ($tagName === 'question') {
					$lastValue = end($tagValues);
					reset($tagValues);

					$this->info("Looking for slides linked to the question #{$lastValue}");
					$lastQuestion = QuizQuestion::find($lastValue);

					if (!$lastQuestion) {
						$this->error("Question {$lastValue} not found...");
						continue;
					}

					$possibleMatches = $lastQuestion->slides()->get();
					$this->info("Found " . count($possibleMatches) . " matching slides.");

					$similarities = [];

					$possibleMatches->sortBy(function($match) use ($cleanSlide, &$similarities) {
						$similarity = 0;
						similar_text($cleanSlide, $match->content, $similarity);
						$this->info('Slide #' . $match->id . ' is similar in ' . $similarity . '%');
						$similarities[] = $similarity;
						return $similarity;
					});

					rsort($similarities);
					$topMatch = $possibleMatches->first();

					if (intval($similarities[0]) < 95) {
						$this->error("There's no slide matching in more than 95%. The content of the top match is the following:");
						$this->info($topMatch->content);
						$this->info("ORIGINAL:");
						$this->info($cleanSlide);
						if (!$this->confirm('Do you wish to use the top match?')) {
							continue;
						}
					}

					$alreadyMatchedQuestions = $topMatch->quizQuestions()->pluck('quiz_questions.id')->toArray();

					$missingQuestions = array_diff($tagValues, $alreadyMatchedQuestions);

					if (!count($missingQuestions)) {
						$this->info("All questions already matched, skipping...");
						continue;
					}

					$this->info('Linking questions ' . implode(', ', $missingQuestions));

					$topMatch->quizQuestions()->attach($missingQuestions);
				}
			}

			$bar->advance();
		}

		$bar->finish();
	}
}
