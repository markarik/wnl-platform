<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\Page;


class PageTransformer extends ApiTransformer
{
	protected $parent;

	protected $availableIncludes = ['discussion'];

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(Page $page)
	{
		$data = [
			'id'         => $page->id,
			'content'    => $page->content,
			'name'       => $page->name,
			'created_at' => $page->created_at->timestamp ?? null,
			'updated_at' => $page->updated_at->timestamp ?? null,
			'discussion_id' => $page->discussion_id
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		if(self::shouldInclude('tags')) {
			$data = array_merge($data, ['tags' => $page->tags->toArray()]);
		}

		return $data;
	}
}
