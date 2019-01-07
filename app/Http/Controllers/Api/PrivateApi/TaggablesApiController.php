<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Course\MoveTaggable;
use App\Models\Tag;
use App\Models\Taggable;
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


		$targetTag = Tag::find($request->get('target_tag_id'));

		$transactionResult = DB::transaction(function () use ($targetTag, $sourceTag) {

			$idsToDelete = DB::table('taggables as t1')->select('t1.id')
				->join('taggables as t2', function($join) {
					$join->on('t1.taggable_id', '=', 't2.taggable_id')->on('t1.taggable_type', '=', 't2.taggable_type');
				})
				->where('t1.tag_id', $sourceTag->id)
				->where('t2.tag_id', $targetTag->id)
				->pluck('id');

			$taggablesDeleted = Taggable::whereIn('id', $idsToDelete)->delete();

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
