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
	protected $signature = 'data-migration:create-nested-course-structure';

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
				'course_id'         => 1,
				'structurable_type' => 'App\\Models\\Group',
				'structurable_id'   => $group->id,
			]);
			foreach ($group->lessons as $lesson) {
				CourseStructureNode::create([
					'course_id'         => 1,
					'structurable_type' => 'App\\Models\\Lesson',
					'structurable_id'   => $lesson->id,
				], $groupNode);
				print 'L';
			}
			print 'G';
		}

		// TODO: Below code is for testing purposes. To be removed.
		$kardiologiaGroup = Group::create(['name' => 'Kardiologia', 'course_id' => 1]);
		$kardiologiaGroupNode = CourseStructureNode::create([
			'course_id'         => 1,
			'structurable_type' => 'App\\Models\\Group',
			'structurable_id'   => $kardiologiaGroup->id,
		]);

		$internaGroupNode = CourseStructureNode::select()
			->where('structurable_type', 'App\\Models\\Group')
			->where('structurable_id', 1)
			->first();

		$internaGroupNode->prependNode($kardiologiaGroupNode);

		$kardiologiaLessonNodes = CourseStructureNode::select()
			->where('structurable_type', 'App\\Models\\Lesson')
			->whereIn('structurable_id', [1, 2, 3, 76, 77])
			->get();

		foreach ($kardiologiaLessonNodes as $lessonNode) {
			$lessonNode->appendToNode($kardiologiaGroupNode)->save();
		}

		print PHP_EOL;
		return;
	}
}
