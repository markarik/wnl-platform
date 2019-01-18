<?php

namespace App\Console\Commands;

use App\Models\CourseStructureNode;
use App\Models\Group;
use Illuminate\Console\Command;

class MigrateCourseStructure extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'course:migrate-structure';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Migrate data to new tree based course structure';

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
		$groups = Group::all();

		foreach ($groups as $group) {
			$groupNode = CourseStructureNode::create(['name' => $group->name, 'type' => 'group']);
			foreach ($group->lessons as $lesson) {
				CourseStructureNode::create(['name' => $lesson->name, 'type' => 'lesson'], $groupNode);
				print 'L';
			}
			print 'G';
		}

		print PHP_EOL;
		return;
	}
}
