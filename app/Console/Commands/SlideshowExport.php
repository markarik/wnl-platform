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
	const IMAGE_PATTERN = '/<img.*src="(.*?)".*>/';
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
    protected $description = 'Command that exports slideshow with all theirs connections including sides, presentables, sections subsections';

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
			->merge($this->getPresentables($slideshow->id, Slideshow::class))
			->merge($this->getPresentables($sections->pluck('id'), Section::class))
			->merge($this->getPresentables($subsections->pluck('id'), Subsection::class));



		$data = [
			'screen' => $screen->toArray(),
			'slideshow' => $slideshow->toArray(),
			'slides' => $slides->toArray(),
			'sections' => $sections->toArray(),
			'subsections' => $subsections->toArray(),
			'presentables' => $presentables->toArray(),
		];

<<<<<<< HEAD:app/Console/Commands/ExportSlideshow.php
		//delete unwanted keys for screen
		unset($data['screen']['id']);
		unset($data['screen']['discussion_id']);
		unset($data['screen']['is_discussable']);
		unset($data['screen']['sections']);

		//delete unwanted keys for slides

		foreach ($data['slides'] as $slide) {
			unset($slide['id']);
			// dd($slide);
		}

		dd($data['slides']);

=======
		// TODO: Generate sensible filename or take if from argument.
>>>>>>> 3c6ed63805acd43ab22f9b990304cbefc9b5d492:app/Console/Commands/SlideshowExport.php
		Storage::put('exports/slideshow_export.json', json_encode($data));

		return 0;
    }

	private function getSubsections(Collection $sections)
	{
		return Subsection::whereIn('section_id', $sections->pluck('id'))->get();
	}

	private function getPresentables($ids, $modelName)
	{
		return DB::table('presentables')
	            ->where('presentable_type', 'App\\Models\\' . $modelName)
	            ->whereIn('presentable_id', (array) $ids)
	            ->get();
	}
}
