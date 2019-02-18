<?php namespace App\Http\Controllers\Api\Transformers;

use App\Models\Reaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

		/** @var Collection $counts */
		$counts = Reaction::count($reactable);
		$counts = $counts
			->keyBy('type')
			->map(function ($element) {
				return $element->count;
			});

		/** @var Collection $flags */
		$flags = Reaction::flags($reactable);
		$flags = $flags
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
