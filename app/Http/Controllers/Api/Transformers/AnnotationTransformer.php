<?php

namespace App\Http\Controllers\Api\Transformers;

use App\Http\Controllers\Api\ApiTransformer;
use App\Models\Annotation;

class AnnotationTransformer extends ApiTransformer
{
	public function transform(Annotation $annotation)
	{
		$data = [
			'description' => $annotation->description,
			'id' => $annotation->id,
			'keyword' => $annotation->keyword,
		];

		return $data;
	}
}
