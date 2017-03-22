<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Models\Tag;
use League\Fractal\TransformerAbstract;


class TagTransformer extends TransformerAbstract
{
	protected $editionId;
	protected $parent;

	public function __construct($parent = null)
	{
		$this->parent = $parent;
	}

	public function transform(Tag $tag)
	{
		$data = [
			'id'   => $tag->id,
			'name' => $tag->name,
		];

		if ($this->parent) {
			$data = array_merge($data, $this->parent);
		}

		return $data;
	}
}
