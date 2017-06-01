<?php


namespace App\Http\Controllers\Api\Transformers;


use League\Fractal\TransformerAbstract;

class ReactionCountTransformer extends TransformerAbstract
{
	protected $parentResource;
	protected $parentModel;

	public function __construct($parentResource, $parentModel)
	{
		$this->parentResource = $parentResource;
		$this->parentModel = $parentModel;
	}


	public function transform($reactions)
	{
		$data = [
			'id'                  => $this->parentModel->id,
			$this->parentResource => $this->parentModel->id,
		];

		$data = array_merge($data, $reactions);

		return $data;
	}
}
