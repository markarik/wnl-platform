<?php

namespace App\Console\Commands;

use App\Models\TagsTaxonomy;
use Illuminate\Console\Command;

class ExportTagsTaxonomy extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'tags:export';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Export tags taxonomy to tsv file.';

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
		$tagsTax = TagsTaxonomy::all();
		$data = collect();
		$data = $this->getChildren(0, 0, $tagsTax, $data);
		$contents = $data->map(function ($t) {
			return implode("\t", $t);
		})->implode("\n");
		\Storage::put('exports/tags.tsv', $contents);
	}

	protected function getChildren($parentId, $level, $tagsTax, $data)
	{
		$children = $tagsTax->where('parent_tag_id', $parentId);
		if ($children->count() === 0) {
			return $data;
		}

		$level++;

		foreach ($children as $item) {
			$data->push([str_repeat("\t", $level), $item->tag->name]);
			$data = $this->getChildren($item->tag_id, $level, $tagsTax, $data);
		}

		return $data;
	}
}
