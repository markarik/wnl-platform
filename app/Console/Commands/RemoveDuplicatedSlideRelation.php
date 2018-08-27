<?php

namespace App\Console\Commands;

use App\Models\Slide;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveDuplicatedSlideRelation extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'slides:removeDuplicatedRelations';

	public function handle() {
		//duplicated Quiz Questions relation
		$slideIds = DB::table('slide_quiz_question')
			->select('slide_id')
			->get()
			->pluck('slide_id');

		foreach($slideIds as $key => $slideId) {
			$slide = Slide::find($slideId);
			if (!$slide) {
				$this->info("Slide with id: {$slideId} not found");
			} else {
				$uniqueQuestions = $slide->quizQuestions->unique();
				if ($uniqueQuestions->count() !== $slide->quizQuestions->count()) {
					$this->info("Slide has duplicated quiz questions: {$slideId}");
					$slide->quizQuestions()->detach();
					$slide->quizQuestions()->sync($uniqueQuestions->pluck('id'));
				}
			}
		}
	}
}
