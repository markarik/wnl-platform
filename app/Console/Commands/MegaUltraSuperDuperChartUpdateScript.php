<?php

namespace App\Console\Commands;

use Intervention\Image\Exception\NotReadableException;
use Storage;
use App\Models\Slide;
use Illuminate\Console\Command;
use Intervention\Image\Facades\Image;

class MegaUltraSuperDuperChartUpdateScript extends Command
{
	const CHART_PATH_PATTERN = '/storage\/charts\/([^"]*)"/';

	const CHART_URL_PATTERN = '/src="([^"]*)".*class="chart"/';

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'charts:update {id?}';

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
		if ($id = $this->argument('id')) {
			$this->update(Slide::find($id));

			return true;
		}

		$slides = Slide::all();
		$this->info('Searching for charts in ' . $slides->count() . ' slides...' . PHP_EOL);
		foreach ($slides as $slide) {
			$this->update($slide);
		}

		return true;
	}

	protected function update($slide)
	{
		$match = $this->match(self::CHART_PATH_PATTERN, $slide->content);
		if (!$match) return;

		$this->info('Found chart! Updating image...');
		$originalFileName = $match[0][1];
		$chartHash = preg_replace('/.png(.*)/', '', $originalFileName);
		if (!$this->updateFile($chartHash)) return;

		$originalPath = $this->match(self::CHART_URL_PATTERN, $slide->content);
		$newPath = env('APP_URL') . '/storage/charts/' . $chartHash . '.png?cb=' . str_random(32);
		$slide->content = str_replace($originalPath[0][1], $newPath, $slide->content);
		$slide->save();
		$this->info('OK.');
	}

	public function match($pattern, $data)
	{
		$match = [];
		preg_match_all($pattern, $data, $match, PREG_SET_ORDER);

		return $match;
	}

	public function updateFile($chartId)
	{
		try {
			$lucidUrl = 'https://www.lucidchart.com/documents/thumb/%s/0/0/NULL/%d';
			$imageSizePx = 2000;
			$urlFormatted = sprintf($lucidUrl, $chartId, $imageSizePx);
			$image = Image::make($urlFormatted)->stream('png');
			$path = "charts/{$chartId}.png";
			Storage::put('public/' . $path, $image);
		}
		catch (NotReadableException $e) {
			$this->warn($e->getMessage());

			return false;
		}

		return true;
	}
}
