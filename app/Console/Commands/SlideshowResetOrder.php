<?php

namespace App\Console\Commands;

use App\Models\Presentable;
use Illuminate\Console\Command;

class SlideshowResetOrder extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'slideshow:resetOrder {slideshowId}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

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
		$slideshowId = $this->argument('slideshowId');
		$whereClause = [
			['presentable_type', 'App\\Models\\Slideshow'],
			['presentable_id', '=', (int) $slideshowId],
		];

		$presentables = Presentable::select()
			->where($whereClause)
			->orderBy('order_number', 'asc')
			->get();

		foreach ($presentables as $index => $presentable) {
			$presentable->order_number = $index;
			$presentable->save();
		}

		\Cache::tags(json_encode(['where' => $whereClause]))->flush();

		return;
	}
}
