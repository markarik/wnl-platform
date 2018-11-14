<?php

namespace App\Console\Commands;

use App\Models\QuizQuestion;
use App\Models\Tag;
use Illuminate\Console\Command;
use Storage;

class TagsAdd extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'tags:add';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create new tags based on model name and attach to model.';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		
	}
}
