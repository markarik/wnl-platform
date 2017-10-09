<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Slide;
use App\Http\Controllers\Api\ApiTransformer;

class SlideTransformer extends ApiTransformer
{
	protected $parent;
	protected $withContext;

	public function __construct($parent = null, $withContext = false)
	{
		$this->parent = $parent;
		$this->withContext = $withContext;
	}

	public function transform(Slide $slide)
	{
		$data = [
			'content'       => $slide->content,
			'is_functional' => $slide->is_functional,
			'snippet'       => $slide->snippet,
			'id'            => $slide->id
		];

		if (!empty($this->withContext)) {
			$searchable = $slide->toSearchableArray();
			$data['context'] = !empty($searchable) ? $searchable['context'] : [];
		}

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
