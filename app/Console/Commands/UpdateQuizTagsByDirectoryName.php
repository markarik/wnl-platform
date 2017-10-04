<?php

namespace App\Console\Commands;

use App\Models\QuizQuestion;
use Illuminate\Console\Command;
use Storage;

class UpdateQuizTagsByDirectoryName extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'quiz:tagsByDir';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';
	protected $storage;
	protected $questions;
	protected $data;

	const SPREADSHEET_MIME = 'application/vnd.google-apps.spreadsheet';
	protected $structure;

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
		$this->storage = Storage::disk('google');
		$this->questions = QuizQuestion::all();
		$this->data = collect();
		$this->structure = collect();

		$startDir = '0B0rmpGZRYtHjY2wybnlFclNqQUU';
		$this->info('Scanning folder structure...');
		$this->scanFolders($startDir);

		$filesCount = $this->structure->flatten()->count();
		$this->info('Downloading ' . $filesCount . ' files...');
		$this->fetchAndMatch();

		$itemsCount = $this->data->flatten()->count();
		Storage::put('exports/exams.json', $this->data->toJson());
		$this->info('Done. Map containing '. $itemsCount .' items saved to exports/exams.json');
	}

	protected function scanFolders($path = '/', $tag = '', $level = 0)
	{
		$excluded = ['wszystkie', 'Wzór', 'Zestawy', 'zestawy', 'Posegregowane', 'wszystko',
			'Podział', 'podzielone', 'Bezpańskie', 'Wszystkie', 'do lekcji', 'Lekcja'];

		$items = $this->storage->listContents($path);

		foreach ($items as $item) {
			$filename = $item['filename'];
			if (str_contains(strtolower($filename), ['jesień', 'wiosna'])) {
				$tag = $filename;
				$this->info(str_repeat(' ', $level) . ' 🎌 Found tag! ' . $tag);
			}

			if ($tag !== '' &&
				array_has($item, ['mimetype']) &&
				!str_contains($item['filename'], $excluded) &&
				$item['mimetype'] === self::SPREADSHEET_MIME
			) {
				$this->pushKey($tag, 'structure');
				$this->structure[$tag]->push($item['path']);
				$this->info(str_repeat(' ', $level) . ' 💾 Saved item ' . $item['filename'] . ' under tag ' . $tag);
			}

			if ($item['type'] === 'dir' &&
				!str_contains($item['filename'], $excluded)
			) {
				$this->info(str_repeat(' ', $level) . ' 📁 Entering folder ' . $filename);
				$this->scanFolders($item['path'], $tag, $level + 1);
			}

			if ($level === 0) $tag = '';

//			return;
		}
	}

	protected function fetchAndMatch()
	{
		$remove = ['Baza pytan ', 'Baza pytań - pedaitria ', 'Baza pytań '];
		foreach ($this->structure as $tag => $files) {
			$tag = strtolower(str_replace($remove, '', trim($tag)));
			foreach ($files as $file) {
				$contents = stream_get_contents($this->storage->readStream($file));
				$this->matchQuestions($tag, $contents);
				print '.';
			}
		}

		print PHP_EOL;
	}

	protected function matchQuestions($filename, $contents)
	{
		$this->pushKey($filename, 'data');
		foreach ($this->questions as $question) {
			if (str_contains(nl2br($contents), $question->text)) {
				$this->data[$filename]->push($question->text);
			}
		}
	}

	protected function pushKey($key, $prop)
	{
		if (!$this->$prop->has($key)) {
			$this->$prop->put($key, collect());
		}
	}
}
