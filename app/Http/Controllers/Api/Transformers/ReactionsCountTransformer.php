<?php namespace App\Http\Controllers\Api\Transformers;

use App\Models\Reaction;
use Illuminate\Database\Eloquent\Model;

class ReactionsCountTransformer
{
	/**
	 * Get formatted reactions counters and flags;
	 *
	 * @param Model $reactable
	 * @return array
	 */
	public static function transform($reactable)
	{
		$reactions = [];
		$types = Reaction::get(['type'])
			->map(function ($element) {
				return $element->type;
			});

		$counts = Reaction::count($reactable)
			->keyBy('type')
			->map(function ($element) {
				return $element->count;
			});

		$flags = Reaction::flags($reactable)
			->keyBy('type')
			->map(function ($element) {
				return $element->count;
			});

		foreach ($types as $type) {
			$reactions[$type] = [
				'count'      => (int)$counts->get($type),
				'hasReacted' => (bool)$flags->get($type),
			];
		}

		return $reactions;
	}
}
