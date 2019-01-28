<?php

namespace App\Http\Controllers\Api\Concerns;


trait OperatesOnNestedSets
{
	public function postNode($request)
	{
		$parentTaxonomyTerm = null;
		if ($request->parent_id) {
			$parentTaxonomyTerm = (self::MODEL_CLASS)::find($request->parent_id);
		}

		$taxonomyTerm = (self::MODEL_CLASS)::create($request->all(), $parentTaxonomyTerm);

		return $this->transformAndRespond($taxonomyTerm);
	}

	public function putNode($request)
	{
		$taxonomyTerm = (self::MODEL_CLASS)::find($request->route('id'));
		$newParentId = $request->get('parent_id');

		if ($newParentId !== $taxonomyTerm->parent_id) {
			if ($newParentId === null) {
				$taxonomyTerm->makeRoot();
			} else {
				$taxonomyTerm->appendToNode((self::MODEL_CLASS)::find($newParentId));
			}
		}

		if (!$taxonomyTerm) {
			return $this->respondNotFound();
		}

		$taxonomyTerm->update($request->all());

		return $this->transformAndRespond($taxonomyTerm);
	}

	public function moveNode($request)
	{
		$target = (self::MODEL_CLASS)::find($request->get('id'));
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