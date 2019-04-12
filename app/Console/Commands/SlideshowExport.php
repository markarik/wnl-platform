<?php

namespace App\Console\Commands;

use App\Models\Section;
use App\Models\Slideshow;
use Illuminate\Console\Command;
use App\Models\Screen;
use App\Models\Subsection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use DB;
use phpDocumentor\Reflection\Types\Mixed_;
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
		$slides = $slideshow->slides;
		$sections = $screen->sections;
		$subsections = $this->getSubsections($sections);
		$presentables = collect()
			->merge($this->getPresentables([$slideshow->id], Slideshow::class))
			->merge($this->getPresentables($sections->pluck('id')->toArray(), Section::class))
			->merge($this->getPresentables($subsections->pluck('id')->toArray(), Subsection::class));

		$data = [
			'screen' => $this->removeUnwantedFieldsFromScreen($screen),
			'slideshow' => $this->removeId($slideshow),
			'slides' => $this->removeId($slides),
			'sections' => $this->removeId($sections),
			'subsections' => $this->removeId($subsections),
			'presentables' => $this->removeId($presentables),
		];


		// TODO: Generate sensible filename or take if from argument.
		Storage::put('exports/slideshow_export.json', json_encode($data));

		return 0;
	}

	/**
	 * @param $data
	 * @return Collection|Model
	 */
	private function removeId($data)
	{
		if ($data instanceof Collection) {
			return $data->except(['id'])->toArray();
		}

		if ($data instanceof Model) {
			$data->toArray();
			unset($data['id']);
			return $data;
		}
	}

	/**
	 * @param Screen $screen
	 * @return array
	 */
	private function removeUnwantedFieldsFromScreen(Screen $screen):array
	{
		$screenData = $screen->toArray();

		unset($screenData['id']);
		unset($screenData['discussion_id']);
		unset($screenData['is_discussable']);
		unset($screenData['sections']);

		return $screenData;
	}

	/**
	 * @param Collection $sections
	 * @return Collection
	 */
	private function getSubsections(Collection $sections):Collection
	{
		return Subsection::whereIn('section_id', $sections->pluck('id'))->get();
	}

	/**
	 * @param $ids
	 * @param $modelName
	 * @return Collection
	 */
	private function getPresentables(array $ids, string $modelName):Collection
	{
		return DB::table('presentables')
			->where('presentable_type', 'App\\Models\\' . $modelName)
			->whereIn('presentable_id', $ids)
			->get()
			->except(['id']);
	}
}
