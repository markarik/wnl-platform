<?php

namespace App\Console\Commands;

use App\Models\QuizQuestion;
use App\Models\Tag;
use Illuminate\Console\Command;
use Storage;

class TagsCreateAndAttachToQuestions extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'questions:attachTags
		{--F|filename= : Name of a file with a comma separated list of ids of questions to be tagged}
		{--T|tagsNames= : Comma separated list of new tags to create and attach}
	';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create new tags and attach them to a list of questions.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$filename = $this->option('filename');
		$questionsIds = explode(',', Storage::get($filename));
		$tagsNames = explode(',', $this->option('tagsNames'));

		foreach ($tagsNames as $tag) {
			$tagModel = Tag::firstOrCreate(['name' => trim($tag)]);
			$bar = $this->output->createProgressBar(count($questionsIds));

			// Used this way of iteration and not whereIn,
			// to preserve the original order of the provided IDs.
			foreach ($questionsIds as $id) {
				$question = QuizQuestion::find($id);
				if (!$question) {
					$this->error("\nQuestion {$id} does not exist.\n");
				} elseif ($question->tags->contains($tagModel)) {
					$this->info("\nQuestion {$id} is already tagged.\n");
				} else {
					$question->tags()->attach($tagModel);
				}

				$bar->advance();
			}
		}
	}
}
