<?php

namespace App\Observers;

use App\Models\Slide;
use App\Jobs\DeleteModels;
use App\Jobs\DetachReactions;
use Illuminate\Foundation\Bus\DispatchesJobs;

class SlideObserver
{
	use DispatchesJobs;


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
