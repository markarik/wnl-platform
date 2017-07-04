<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Category;
use App\Http\Controllers\Api\ApiTransformer;


class CategoryTransformer extends ApiTransformer
{
	public function transform(Category $category)
	{
		$data = [
			'id'   => $category->id,
			'name' => $category->name,
			'parent_id' => $category->parent_id
		];

		return $data;
	}
}
