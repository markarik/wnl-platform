<?php

namespace App\Console\Commands;

use Storage;
use Carbon\Carbon;
use App\Models\Lesson;
use Illuminate\Console\Command;

class DumpCourseStructure extends Command
{
	const DIR = 'course_dumps';
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'course:structure {action?} {file?} {--group=}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->error('This Command isn\'t ready for tree course structure');
		return;

		$fileName = $this->argument('file');
		$action = $this->argument('action');

		if (!$action) {
			$this->error('Argument {action} is required.');
			exit;
		}

		if ($action === 'restore' && !$fileName) {
			$this->error('Argument {file} is required.');
		}

		if ($action === 'dump') {
			$this->dump($fileName);
			exit;
		}

		if ($action === 'list') {
			$this->list();
			exit;
		}

		if ($action === 'restore') {
			$this->restore($fileName);
			exit;
		}

		$this->info('Usage: php artisan order:structure {method (dump|restore|list)} {file}');

		return;
	}

	protected function dump($fileName)
	{
		if (!$fileName) {
			$fileName = Carbon::now()->format('Y_m_d_h_i');
		}
		$fileName = snake_case($fileName);
		$lessons = Lesson::with('screens');

		if ($group = $this->option('group')) {
			$lessons = $lessons->where('group_id', $group);
		}

		$env = env('APP_ENV');
		Storage::disk('s3')->put(self::DIR . "/{$env}_{$fileName}.json", $lessons->get()->toJson());
	}

	protected function restore($fileName)
	{
		$data = Storage::disk('s3')->get(self::DIR . "/{$fileName}");
		$decoded = json_decode($data);
		foreach ($decoded as $lesson) {
			$lessonModel = Lesson::updateOrCreate(
				['name' => $lesson->name],
				[
					'name'       => $lesson->name,
					'group_id'   => $lesson->group_id,
					'created_at' => $lesson->created_at,
					'updated_at' => $lesson->updated_at,
				]);
			foreach ($lesson->screens as $screen) {
				$lessonModel->screens()->updateOrCreate(
					['name' => $screen->name],
					[
						'name'         => $screen->name,
						'order_number' => $screen->order_number,
						'type'         => $screen->type,
						'meta'         => $screen->meta,
						'content'      => $screen->content,
						'created_at'   => $screen->created_at,
						'updated_at'   => $screen->updated_at,
					]);
			}
		}
	}

	protected function list()
	{
		$files = Storage::disk('s3')->files(self::DIR);
		$this->info('Saved course dumps:');
		foreach ($files as $file) {
			print basename($file) . PHP_EOL;
		}
	}
}
