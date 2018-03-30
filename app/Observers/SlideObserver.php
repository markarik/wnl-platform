<?php

namespace App\Observers;

use App\Events\Slides\SlideUpdated;
use App\Jobs\DeleteModels;
use App\Jobs\DetachReactions;
use App\Models\Slide;
use Illuminate\Foundation\Bus\DispatchesJobs;

class SlideObserver
{
	use DispatchesJobs;

	public function updated(Slide $slide)
	{
		if ($slide->isDirty(['content', 'is_functional'])) {
			event(new SlideUpdated($slide));
		}
	}

	public function created(Slide $slide)
	{
		//
	}

	public function deleted(Slide $slide)
	{
		$this->dispatch(new DetachReactions($slide));
		$this->dispatch(new DeleteModels($slide->comments));
	}
}
