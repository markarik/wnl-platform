<?php

namespace App\Console\Commands;

use App\Models\LessonAvailability;
use App\Models\Lesson;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkLessonsAsRequired extends Command
{
	/**
	* The name and signature of the console command.
	*
	* @var string
	*/
	protected $signature = 'lesson:mark-as-required';

	/**
	* The console command description.
	*
	* @var string
	*/
	protected $description = 'Add is_required flag to existing lessons';

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

		$lessonAvailabilities = LessonAvailability::whereDate('start_date', '>', '2017-11-04')
			->get();

		foreach($lessonAvailabilities as $lessonAvailability) {
			Lesson::where('id', $lessonAvailability->lesson_id)
				->update(['is_required' => true]);
		}
		return true;
	}
}
