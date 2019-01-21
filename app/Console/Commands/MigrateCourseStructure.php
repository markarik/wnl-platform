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
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$groups = Group::all();

		foreach ($groups as $group) {
			$groupNode = CourseStructureNode::create([
				'type' => 'group',
				'course_id' => 1,
				'structurable_type' => 'App\\Models\\Group',
				'structurable_id' => $group->id,
			]);
			foreach ($group->lessons as $lesson) {
				CourseStructureNode::create([
					'type' => 'lesson',
					'course_id' => 1,
					'structurable_type' => 'App\\Models\\Lesson',
					'structurable_id' => $lesson->id,
				], $groupNode);
				print 'L';
			}
			print 'G';
		}

		print PHP_EOL;
		return;
	}
}
