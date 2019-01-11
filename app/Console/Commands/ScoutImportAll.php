<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ScoutImportAll extends Command
{
	protected $signature = 'scout:import-all';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import all searchable models to search index';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$traitClassNames = [\ScoutEngines\Elasticsearch\Searchable::class, \Laravel\Scout\Searchable::class];

		// Find all php files in Models directory
		$directoryIterator = new RecursiveDirectoryIterator(app_path('Models/'),RecursiveDirectoryIterator::SKIP_DOTS);
		$iterator = new RecursiveIteratorIterator($directoryIterator);

		foreach($iterator as $file) {
			if ($file->getExtension() === 'php') {
				require_once($file->getPathname());
			}
		}

		// Get classes using Searchable trait
		$searchableClassNames = array_filter(
			get_declared_classes(),
			function ($className) use ($traitClassNames) {
				$traits = class_uses($className);
				return count(array_intersect($traits, $traitClassNames));
			}
		);

		// Call scout:import on every searchable class
		array_walk($searchableClassNames, function ($searchableClassName) {
			$this->call('scout:import', ['model' => $searchableClassName]);
		});
	}
}
