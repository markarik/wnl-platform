<?php

namespace App\Console\Commands;

use App;
use Storage;
use App\Models\Slide;
use Illuminate\Console\Command;
use Facades\Lib\SlideParser\Parser;
use Intervention\Image\Facades\Image;
use App\Notifications\ChartsUpdatingDone;
use Illuminate\Notifications\Notifiable;
use Intervention\Image\Exception\NotReadableException;

class MegaUltraSuperDuperChartUpdateScript extends Command
{
	use Notifiable;

	const CHART_PATH_PATTERN = '/public\/charts\/([^"]*)"/';

	const CHART_URL_PATTERN = '/src="([^"]*)".*class="chart"/';

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'charts:update {id?} {--notify}';

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
		\Log::debug('Running charts updater with "notify" option set to ' . $this->option('notify'));

		if ($id = $this->argument('id')) {
			$this->update(Slide::find($id));

			return true;
		}

		$slides = Slide::all();
		$this->info('Searching for charts in ' . $slides->count() . ' slides...' . PHP_EOL);
		foreach ($slides as $slide) {
			$this->update($slide);
		}

		if ($this->option('notify')) {
			$this->notify(new ChartsUpdatingDone());
		}

		\Log::debug('Charts updater done.');

		return true;
	}

	protected function update($slide)
	{
		$match = $this->match(self::CHART_PATH_PATTERN, $slide->content);
		$this->tryParsingLucidIframe($slide);
		if (!$match) return;

		$this->info('Found chart! Updating image...');
		$originalFileName = $match[0][1];
		$chartHash = preg_replace('/.png(.*)/', '', $originalFileName);
		$newPath = $this->updateFile($chartHash);
		if (!$newPath) return;

		$originalPath = $this->match(self::CHART_URL_PATTERN, $slide->content);
		$cb = str_random(32);
		$slide->content = str_replace($originalPath[0][1], "{$newPath}?cb={$cb}", $slide->content);
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
			Storage::put('public/' . $path, $image->__toString(), 'public');
		}
		catch (NotReadableException $e) {
			$this->warn($e->getMessage());

			return false;
		}

		return $path;
	}

	public function routeNotificationForSlack()
	{
		if (App::environment('production')) {
			return env('SLACK_SLIDESHOWS');
		}

		return env('SLACK_TEST');
	}

	public function tryParsingLucidIframe($slide)
	{
		$result = Parser::handleCharts($slide->content);

		if ($result !== $slide->content) {
			$slide->content = $result;
			$slide->save();
		}
	}
}
