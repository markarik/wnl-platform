<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Screen;
use App\Models\Subsection;
use Illuminate\Support\Collection;
use DB;
use Storage;

class ExportSlideshow extends Command
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
		$images = [];

		$data = [
			'screen' => $screen->toArray(),
			'slideshow' => $slideshow->toArray(),
			'slides' => $slides->toArray(),
			'sections' => $sections->toArray(),
			'subsections' => $subsections->toArray(),
			'presentables' => $presentables->toArray(),
			'images' => $images,
		];

		Storage::put('exports/slideshow_export.json', json_encode($data));

		return 0;
    }

	private function getSubsections(Collection $sections)
	{
		return Subsection::whereIn('section_id', $sections->pluck('id'))->get();
	}

	private function getPresentables($ids, $modelName)
	{
		$presentables = DB::table('presentables')
	            ->where('presentable_type', 'App\\Models\\' . $modelName)
	            ->whereIn('presentable_id', (array) $ids)
	            ->get();
	}
}
