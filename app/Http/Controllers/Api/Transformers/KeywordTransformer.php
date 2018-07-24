<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Keyword;
use App\Http\Controllers\Api\ApiTransformer;


class KeywordTransformer extends ApiTransformer
{
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(Keyword $keyword)
	{
		$data = [
			'id'   => $keyword->id,
			'text' => $keyword->text,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
