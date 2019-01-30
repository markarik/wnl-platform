<?php

namespace App\Http\Controllers\Api\Concerns;


trait OperatesOnNestedSets
{
	public function postNode($request)
	{
		$parentTaxonomyTerm = null;
		if ($request->parent_id) {
			$parentTaxonomyTerm = (self::getResourceModel($this->resourceName))::find($request->parent_id);
		}

		$node = (self::getResourceModel($this->resourceName))::create($request->all(), $parentTaxonomyTerm);

		return $this->transformAndRespond($node);
	}

	public function putNode($request)
	{
		$node = (self::getResourceModel($this->resourceName))::find($request->route('id'));
		$newParentId = $request->get('parent_id');

		if (!$node) {
			return $this->respondNotFound();
		}

		if ($newParentId !== $node->parent_id) {
			if ($newParentId === null) {
				$node->makeRoot();
			} else {
				$node->appendToNode((self::getResourceModel($this->resourceName))::find($newParentId));
			}
		}

		$node->update($request->all());

		return $this->transformAndRespond($node);
	}

	public function moveNode($request)
	{
		$target = (self::getResourceModel($this->resourceName))::find($request->get('id'));
		$direction = $request->get('direction');

		if ($direction === 0) {
			return $this->respondOk();
		}

		if ($direction > 0) {
			$success = $target->down($direction);
		} else {
			$success = $target->up(abs($direction));
		}

		if (!$success) {
			return $this->respondUnprocessableEntity('direction out of range');
		}

		return $this->respondOk();
	}
}