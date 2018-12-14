<?php
namespace Checks\DataIntegrity;

class PresentablesOrderNumberCheck extends DataIntegrityCheck {

	public function check() {
		$incorrectPresentables = \DB::table('presentables')
			->selectRaw('count(1) as count, max(order_number) as max, presentable_type, presentable_id')
			->whereNotIn('presentable_type', ['App\\Models\\Section', 'App\\Models\\Subsection'])
			->groupBy(['presentable_type', 'presentable_id'])
			->havingRaw('count - 1 != max')
			->get();

		if ($incorrectPresentables->count() > 0) {
			$this->handleError(__METHOD__, [
				'presentables' => $incorrectPresentables->map(function($presentable) {
					return [
						'presentable_id' => $presentable->presentable_id,
						'presentable_type' => $presentable->presentable_type
					];
				})->toArray()
			]);
		}
	}
}
