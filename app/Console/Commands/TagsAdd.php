<?php

namespace App\Console\Commands;

use App\Models\Contracts\WithTags;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

class TagsAdd extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'tags:add-from-name {--model=}';

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
		$modelName = $this->option('model');
		$modelClass = "\\App\\Models\\$modelName";

		/** @var Model[]|WithTags[]|Collection $models */
		$models = $modelClass::all();
		$createdTags = [];

		$bar = $this->output->createProgressBar($models->count());

		foreach ($models as $model) {
			$bar->advance();

			if (empty($model->name)) {
				continue;
			}

			$tag = Tag::firstOrCreate(['name' => trim($model->name)]);

			try {
				$model->tags()->attach($tag);
				$createdTags[] = [$tag->name];
			} catch (QueryException $exception) {
				$this->warn("Model $modelName::{$model->id} has tag: {$tag->name}");
			}
		}

		$bar->finish();

		echo PHP_EOL;

		$this->table(['created tags'], $createdTags);
	}
}
