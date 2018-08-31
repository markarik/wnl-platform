<?php

namespace App\Console\Commands;

use App\Models\Slide;
use Illuminate\Console\Command;

class SlidesRemoveUnused extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'slides:removeUnused';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Delete slides that are not attached to any slideshow.';

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
		$slides = Slide::whereDoesntHave('slideshow')->get();

		$message = $slides->count() . ' slides will be deleted.';
		if (!$this->confirm($message)) {
			exit;
		}
		$ids = $slides->pluck('id')->toArray();

		\DB::table('taggables')
			->where('taggable_type', 'App\\Models\\Slide')
			->whereIn('taggable_id', $ids)
			->delete();

		\DB::table('reactables')
			->where('reactable_type', 'App\\Models\\Slide')
			->whereIn('reactable_id', $ids)
			->delete();

		\DB::table('slide_quiz_question')
			->whereIn('slide_id', $ids)
			->delete();

		Slide::whereDoesntHave('slideshow')->delete();

		return;
	}
}
