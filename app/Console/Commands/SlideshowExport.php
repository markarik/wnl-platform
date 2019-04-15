<?php

namespace App\Console\Commands;

use App\Models\Section;
use App\Models\Slideshow;
use Illuminate\Console\Command;
use App\Models\Screen;
use App\Models\Subsection;
use Illuminate\Support\Collection;
use DB;
use Storage;

class SlideshowExport extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'slideshow:export {screenId}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = '
		Command that exports slideshow with all theirs connections including sides, 
		presentables, sections subsections
		';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$screenId = $this->argument('screenId');
		$screen = Screen::find($screenId);

		if (is_null($screen)) {
			$this->warn('Screen not found.');
			return 1;
		}

		$slideshow = $screen->slideshow;
		$slides = $slideshow->slides()->get();
		$sections = $screen->sections()->get();
		$subsections = $this->getSubsections($sections);
		$presentables = collect()
			->merge($this->getPresentables([$slideshow->id], Slideshow::class))
			->merge($this->getPresentables($sections->pluck('id')->toArray(), Section::class))
			->merge($this->getPresentables($subsections->pluck('id')->toArray(), Subsection::class));

		$data = [
			'screen' => $this->removeUnwantedFieldsFromScreen($screen),
			'slideshow' => $this->getSlideshowWithBackgroundUrl($slideshow),
			'slides' => $slides->toArray(),
			'sections' => $sections->toArray(),
			'subsections' => $subsections->toArray(),
			'presentables' => $presentables->toArray(),
		];

		// TODO: Generate sensible filename or take if from argument.
		Storage::put('exports/slideshow_export.json', json_encode($data));

		return 0;
	}

	/**
	 * @param Screen $screen
	 * @return array
	 */
	private function removeUnwantedFieldsFromScreen(Screen $screen): array
	{
		$screenData = $screen->toArray();

		unset($screenData['discussion_id']);
		unset($screenData['is_discussable']);
		unset($screenData['sections']);

		return $screenData;
	}

	private function getSlideshowWithBackgroundUrl(Slideshow $slideshow): array
	{
		$slideshowData = $slideshow->toArray();
		$slideshowData['background_url'] = $slideshow->background_url;
		return $slideshowData;
	}

	/**
	 * @param Collection $sections
	 * @return Collection
	 */
	private function getSubsections(Collection $sections): Collection
	{
		return Subsection::whereIn('section_id', $sections->pluck('id'))->get();
	}

	/**
	 * @param array $ids
	 * @param string $modelName
	 * @return Collection
	 */
	private function getPresentables(array $ids, string $modelName): Collection
	{
		return DB::table('presentables')
			->where('presentable_type', $modelName)
			->whereIn('presentable_id', $ids)
			->get();
	}
}
