<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Course\MoveTaggable;
use App\Models\Tag;
use App\Models\Taggable;
use Illuminate\Http\Request;

class TaggablesApiController extends ApiController {
	public function __construct(Request $request) {
		parent::__construct($request);
		$this->resourceName = config('papi.resources.taggables');
	}

	public function batchMove(MoveTaggable $request, $sourceTagId) {
		$sourceTag = Tag::find($sourceTagId);

		if (!$sourceTag) {
			return $this->respondNotFound('Source tag does not exist');
		}

		$targetTag = Tag::find($request->get('target_tag_id'));

		$taggablesMoved = Taggable::where('tag_id', $sourceTag->id)
			// TODO Is this necessary? If so, replace this comment with the reason for it
			->whereNotIn('taggable_type', Taggable::PROTECTED_TAGGABLE_TYPES)
			// Warning: mass update doesn't fire model events
			// At this point, there are no events for the Taggable model
			->update(['tag_id' => $targetTag->id]);

		return $this->respondOk([
			'taggables_moved' => $taggablesMoved
		]);
	}
}
