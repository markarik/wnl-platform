<?php

namespace App\Console\Commands;

use App\Models\Tag;
use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use Exception;
use Illuminate\Console\Command;
use Storage;

class ImportTaxonomiesStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:taxonomies-structure {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Import taxonomies structure from a csv (1st row headers - ignoring)\n
Example csv:

Taksonomia kliniczna,,,,,,
,Interna,,,,,
,,Kardiologia,,,,";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
			$file = $this->argument('file');

			$contents = Storage::get($file);

			$csv = array_map('str_getcsv', explode("\n",$contents));
			// CSV headers => ignore
			array_shift($csv);


			$bar = $this->output->createProgressBar(count($csv));
			$bar->start();
			$parentTerms = [];

			foreach ($csv as $item) {
				if (!empty($item[0])) {
					$taxonomy = Taxonomy::firstOrCreate(['name' => $item[0]]);

					TaxonomyTerm::where(['taxonomy_id' => $taxonomy->id])->delete();

					$bar->advance();
					continue;
				}

				if (!isset($taxonomy)) {
					throw new Exception('You have to define taxonomy in the 2nd row of file');
				}

				$filtered = array_filter($item, function ($value) {
					return $value !== "";
				});

				$currentLevel = current(array_keys($filtered));
				$tagName = current($filtered);

				if (empty($tagName)) {
					// Empty row => ignore
					$bar->advance();
					continue;
				}

				$tag = Tag::firstOrCreate(['name' => $tagName]);

				$taxonomyTerm = TaxonomyTerm::create([
					'taxonomy_id' => $taxonomy->id,
					'tag_id' => $tag->id
				], $parentTerms[$currentLevel - 1] ?? null);

				$parentTerms[$currentLevel] = $taxonomyTerm;

				$bar->advance();
			}

			$bar->finish();
    }
}
