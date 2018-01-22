<?php namespace App\Console\Commands;

use Storage;
use Artisan;
use Lib\SlideParser\Parser;
use Illuminate\Console\Command;


class SlidesImport extends Command
{
	const BASE_DIRECTORY = 'slideshows';

	protected $parser;

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'slides:import {dir?} {--id=}*';

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

		$screenId = $this->option('id');

		$files = Storage::disk('s3')->files($path);
		$this->info('Importing slideshows...');
		$bar = $this->output->createProgressBar(count($files));
		if ($files && $screenId) {
			$this->info('Screen Id option is only supported when path to one file passed. Screen Id will be ignored');
		}
		foreach ($files as $file) {
			$this->importFile($file);
			$bar->advance();
			\Log::debug($file . ' processed');
		}
		if (!$files) $this->importFile($path, $screenId);
		print PHP_EOL;

		Artisan::queue('tags:fromCategories');
		Artisan::queue('slides:fromCategory');
		Artisan::queue('cache:tag', ['tag' => 'api']);

		return;
	}

	/**
	 * Import slideshow form file.
	 *
	 * @param $file
	 */
	public function importFile($file, $screenId = null)
	{
		$contents = Storage::disk('s3')->get($file);
		$this->parser->parse($contents, $screenId);
	}
}
