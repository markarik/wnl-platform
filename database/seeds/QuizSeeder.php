<?php

use Illuminate\Database\Seeder;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use App\Models\QuizSet;

class QuizSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		QuizQuestion::flushEventListeners();
		QuizAnswer::flushEventListeners();
		QuizSet::flushEventListeners();
		Artisan::queue('quiz:import');
	}
}
