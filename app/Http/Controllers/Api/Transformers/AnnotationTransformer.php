<?php

namespace App\Http\Controllers\Api\Transformers;

use App\Http\Controllers\Api\ApiTransformer;
use App\Models\Annotation;

class AnnotationTransformer extends ApiTransformer
{
	protected $availableIncludes = ['tags','keywords'];
	protected $parent;

	public function __construct($parent = [])
	{
		$this->parent = $parent;
	}

	public function transform(Annotation $annotation)
	{
		$data = [
			'description' => $annotation->description,
			'id' => $annotation->id,
			'title' => $annotation->title,
		];

		return $data;
	}

	public function includeTags(Annotation $annotation)
	{
		$tags = $annotation->tags;

		return $this->collection($tags, new TagTransformer(['annotations' => $annotation->id]), 'tags');
	}

	public function includeKeywords(Annotation $annotation)
	{
		$keywords = $annotation->keywords;

		return $this->collection($keywords, new KeywordTransformer(['annotations' => $annotation->id]), 'keywords');
	}
}
