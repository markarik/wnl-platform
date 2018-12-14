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
	protected $signature = 'slides:import {dir?} {--id=}* {--enableSlidesMatching}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import slideshows to database from storage.';

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
	 * @param Parser $parser
	 *
	 * @return mixed
	 */
	public function handle(Parser $parser)
	{
		$this->parser = $parser;

		$path = self::BASE_DIRECTORY;

		if ($subDir = $this->argument('dir')) {
			$path .= '/' . $subDir;
		}

		$screenId = $this->option('id');
		$enableSlidesMatching = $this->option('enableSlidesMatching');

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
		if (!$files) $this->importFile($path, $screenId, $enableSlidesMatching);
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
	public function importFile($file, $screenId = null, $enableSlidesMatching = false)
	{
		$contents = Storage::disk('s3')->get($file);
		$this->parser->parse($contents, $screenId, $enableSlidesMatching);
	}
}
