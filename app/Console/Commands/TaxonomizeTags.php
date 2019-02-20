<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Taxonomy;
use App\Models\TagsTaxonomy;
use App\Models\Tag;

class TaxonomizeTags extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'taxonomy:addTags {taxonomyId} {parentId} {tags*}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Add passed tags ID to given parent tag ID and taxonomy ID';

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
		$taxonomyId = $this->argument('taxonomyId');
		$parentTagId = $this->argument('parentId');
		$tags = $this->argument('tags');

		$taxonomy = Taxonomy::find($taxonomyId);
		$parentTag = $parentTagId !== '0' ? Tag::find($parentTagId) : null;

		if (empty($taxonomy)) {
			echo "Passed taxonomy not found. Passed id: $taxonomyId \n";
			return;
		}

		$orderNumber = 0;

		foreach($tags as $tagId) {
			echo "Adding Tag: $tagId \n";

			$tagsTaxonomy = new TagsTaxonomy;
			$tagsTaxonomy->tag_id = Tag::find($tagId)->id;

			if (!empty($parentTag)) {
				$tagsTaxonomy->parent_tag_id = $parentTag->id;
			}

			$tagsTaxonomy->taxonomy_id = $taxonomy->id;
			$tagsTaxonomy->order_number = $orderNumber;

			$tagsTaxonomy->save();

			$orderNumber++;
		}
	}
}
