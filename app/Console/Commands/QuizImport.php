<?php namespace App\Console\Commands;

use App\Models\Tag;
use Storage;
use App\Models\QuizSet;
use Illuminate\Console\Command;

class QuizImport extends Command
{
	const VALUE_DELIMITER = "\t";
	const LINE_DELIMITER = "\r\n";
	const DIRECTORY = 'quiz';

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'quiz:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import quiz sets to database from storage.';

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
	 * @return mixed
	 */
	public function handle()
	{
		$files = Storage::disk('s3')->files(self::DIRECTORY);

		foreach ($files as $file) {
			$this->importFile($file);
		}

		return;
	}

	/**
	 * Import quiz set form file.
	 *
	 * @param $file
	 */
	public function importFile($file)
	{
		$contents = Storage::disk('s3')->get($file);
		$lines = explode(self::LINE_DELIMITER, $contents);
		// Get rid of the headers
		array_shift($lines);

		$quizSet = QuizSet::firstOrcreate([
			'name' => str_replace(self::DIRECTORY . '/', '', $file),
		]);

		foreach ($lines as $line) {
			$this->createQuestion($line, $quizSet);
		}
	}

	public function createQuestion($line, $quizSet)
	{
		$values = explode(self::VALUE_DELIMITER, $line);

		$question = $quizSet->questions()->firstOrCreate([
			'text' => $values[0],
		]);

		for ($i = 1; $i <= 5; $i++) {
			$question->answers()->firstOrCreate([
				'text'       => $values[$i],
				'is_correct' => $values[6] === chr(64 + $i),
			]);
		}

		$tagNames = [
			'LEK-' . $values[8],
			$values[9],
			$values[11],
		];

		foreach ($tagNames as $tagName) {
			$tag = Tag::firstOrCreate(['name' => $tagName]);
			$question->tags()->attach($tag);
		}

		$question->preserve_order = (bool)$values[10];
	}
}
