<?php

namespace App\Console\Commands;

use App\Models\QuizQuestion;
use App\Models\Tag;
use Illuminate\Console\Command;
use Storage;

class ImportQuizTagsFromMap extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'quiz:tagsFromMap {file}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

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
		$filename = $this->argument('file');

		$contents = Storage::drive()->get($filename);
		$map = json_decode($contents, true);

		foreach ($map as $tag => $questions) {
			$tagModel = Tag::firstOrCreate(['name' => 'LEK-' . $tag]);

			foreach ($questions as $question) {
				$questionModel = QuizQuestion::where('text', $question)->first();
				if (!$questionModel) continue;
				if (!$questionModel->tags->contains($tagModel)) {
					$questionModel->tags()->attach($tagModel);
				}
			}
		}
	}
}
