<?php namespace App\Console\Commands;

use Storage;
use Artisan;
use App\Models\QuizQuestion;
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
	protected $signature = 'slides:linkQuestions';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Parses slideshows to update links between quiz questions and slides.';

	/**
	 * Create a new command instance.
	 *
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @param Parser $parser
	 * @return mixed
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function handle(Parser $parser)
	{
		$this->parser = $parser;

		$path = self::BASE_DIRECTORY;
		$dirs = Storage::disk('s3')->listContents($path);
		$dirBar = $this->output->createProgressBar(count($dirs));

		$stats = [];

		foreach ($dirs as $dir) {
			$path = self::BASE_DIRECTORY . '/' . $dir['filename'];

			$files = Storage::disk('s3')->files($path);
			$this->info("Importing slideshows from path {$path}");
			$bar = $this->output->createProgressBar(count($files));

			foreach ($files as $file) {
				$stats[$file] = $this->linkSlidesFromFile($file);
				$bar->advance();
				\Log::debug($file . ' processed');
			}

			$dirBar->advance();
			print PHP_EOL;
		}

		dump($stats);

		$dirBar->finish();

		Artisan::queue('cache:tag', ['tag' => 'api']);

		return;
	}

	/**
	 * Import slideshow form file.
	 *
	 * @param string $file
	 * @return array
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function linkSlidesFromFile($file)
	{
		$stats = [
			'all' => 0,
			'linked' => 0,
			'skipped' => 0,
			'uncertain' => 0,
		];

		$this->info("\n\nParsing {$file}");

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

					$stats['all'] = $stats['all'] + count($tagValues);

					$this->info("\n\nLooking for slides linked to the question #{$lastValue}");
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

					if (intval($similarities[0]) < 80) {
						$this->error("There's no slide matching in more than 95%. The content of the top match is the following:");
						$this->info($this->cleanSlideContent($topMatch->content));
						$this->error("ORIGINAL:");
						$this->info($this->cleanSlideContent($cleanSlide));
						if (!$this->confirm('Do you wish to use the top match?')) {
							$stats['uncertain'] = $stats['uncertain'] + count($tagValues);
							continue;
						}
					}

					$alreadyMatchedQuestions = $topMatch->quizQuestions()->pluck('quiz_questions.id')->toArray();

					$missingQuestions = array_diff($tagValues, $alreadyMatchedQuestions);
					if (!count($missingQuestions)) {
						$this->info("All questions already matched, skipping...");
						$stats['skipped'] = $stats['skipped'] + count($missingQuestions);
						continue;
					}

					$this->info('Linking questions ' . implode(', ', $missingQuestions));
					$stats['linked'] = $stats['linked'] + count($missingQuestions);
					$topMatch->quizQuestions()->attach($missingQuestions);
				}
			}

			$bar->advance();
		}

		$bar->finish();

		dump($stats);

		return $stats;
	}

	public function cleanSlideContent($content) {
		return preg_replace('/[\r\n\t\s]+/', ' ', strip_tags($content));
	}
}
