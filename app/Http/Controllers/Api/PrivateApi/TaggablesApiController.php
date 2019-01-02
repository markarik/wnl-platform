<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Course\MoveTaggable;
use App\Models\Tag;
use App\Models\Taggable;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

		$protectedTaggablesCount = $sourceTag->taggables()
			->whereIn('taggable_type', Taggable::PROTECTED_TAGGABLE_TYPES)
			->count();

		// We don't want a situation where a taggables move causes QnA questions to be merged between lessons
		if ($protectedTaggablesCount > 0) {
			return $this->respondUnprocessableEntity([
				'message' => __('tags.errors.move-protected-taggables')
			]);
		}

		$targetTag = Tag::find($request->get('target_tag_id'));

		$transactionResult = DB::transaction(function () use ($targetTag, $sourceTag) {
			$taggablesDeleted = Taggable::where('tag_id', $sourceTag->id)
				->whereExists(function ($query) use ($targetTag) {
					/** @var $query Builder */
					$query->select(DB::raw(1))
						// If you're doing a DELETE on a table, you can't reference that table in a subquery
						// Force MySQL to use a temporary table
						// https://stackoverflow.com/a/14302701
						->from(DB::raw('(select tag_id from taggables) as t2'))
						->where('t2.tag_id', '=', $targetTag->id);
				})->delete();

			$taggablesMoved = Taggable::where('tag_id', $sourceTag->id)
				// Warning: mass update doesn't fire model events
				// At this point, there are no events for the Taggable model
				->update(['tag_id' => $targetTag->id]);

			return [
				'taggables_deleted' => $taggablesDeleted,
				'taggables_moved' => $taggablesMoved
			];
		});

		return $this->respondOk($transactionResult);
	}
}
