<?php

namespace App\Console\Commands;

use App\Models\Tag;
use App\Models\TagsTaxonomy;
use App\Models\Taxonomy;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Storage;

class ImportTaxonomies extends Command
{
	const VALUE_DELIMITER = "\t";
	const ROW_DELIMITER = "\r\n";
	const BASE_DIRECTORY = 'quiz';

	protected $unmatched;
	protected $data;
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'import:taxonomies {file}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

	/**
	 * Create a new command instance.
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->unmatched = collect();
		$this->data = collect();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$file = $this->argument('file');

		$contents = Storage::drive()->get($file);
		$this->import($contents);

		$this->display();
		if ($this->confirm('Save?')) $this->save();
	}

	protected function import($contents)
	{
		$lines = explode(self::ROW_DELIMITER, $contents);
		$level = 0;
		$this->data->put($level, collect());

		foreach ($lines as $line) {
			$result = $this->processLine($line, $level);
			if ($result === false) {
				// Empty line means following will be children
				// of the values above.
				$level++;
				$this->data->put($level, collect());
			}
		}
	}

	protected function processLine($line, $level)
	{
		$values = explode(self::VALUE_DELIMITER, $line);

		if ($values[0] === '') {
			return false;
		}

		if ($level === 0) {
			$record = $this->processTopLevel($values);
		} else {
			$record = $this->process($values);
		}

		$this->data[$level]->push($record);

		return true;
	}

	protected function processTopLevel($values)
	{
		$taxonomyName = array_shift($values);
		$parent = Taxonomy::firstOrCreate([
			'name' => trim($taxonomyName),
		]);
		$tags = $this->getTags($values);

		return collect(compact('parent', 'tags'));
	}

	protected function process($values)
	{
		$parentName = array_shift($values);
		$parent = Tag::select()
				->where('name', trim($parentName))
				->first() ?? $parentName;
		$tags = $this->getTags($values);

		return collect(compact('parent', 'tags'));
	}

	protected function getTags($values)
	{
		$tags = collect();

		foreach ($values as $value) {
			if ($value === '') continue;

			$tag = Tag::select()
				->where('name', trim($value))
				->first();

			if (!$tag) {
				$this->unmatched->push($value);
			} else {
				$tags->push($tag);
			}
		}

		return $tags;
	}

	protected function display()
	{
		foreach ($this->data as $level) {
			foreach ($level as $item) {
				if ($item['parent'] instanceof Model) {
					$this->warn(
						$item['parent']->name .
						' (' . class_basename($item['parent']) . ')' .
						' (' . $item['parent']->id . ')'
					);
					$this->info(
						$item['tags']
							->map(function ($i) {
								return $i->name . ' (' . $i->id . ')';
							})->implode(', ')
						. PHP_EOL
					);
				} else {
					$this->warn($item['parent'] . ' (unmatched parent)');
				}
			}
			$this->warn(str_repeat('-', 100) . PHP_EOL);
		}

		if ($this->unmatched->count() > 0) {
			$this->warn('Unmatched:');
			$this->info($this->unmatched->implode("\t"));
		}
	}

	protected function save()
	{
		$taxonomiesMap = collect();

		foreach ($this->data as $level => $data) {
			foreach ($data as $item) {
				if ($level === 0) {
					$parentTagId = 0;
					$taxonomyId = $item['parent']->id;
				} else {
					if (!$item['parent'] instanceof Model) {
						continue;
					}
					$parentTagId = $item['parent']->id;
					$taxonomyId = $taxonomiesMap->get($item['parent']->id);
				}

				foreach ($item['tags'] as $tag) {
					$taxonomiesMap->put($tag->id, $taxonomyId);
					$data = [
						'parent_tag_id' => $parentTagId,
						'tag_id'        => $tag->id,
						'taxonomy_id'   => $taxonomyId,
					];
					TagsTaxonomy::updateOrCreate($data, $data);
				}
			}
		}

		$this->info('saved.');
	}
}
