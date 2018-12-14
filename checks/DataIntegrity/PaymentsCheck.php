<?php
namespace Checks\DataIntegrity;

class PaymentsCheck extends DataIntegrityCheck {
	public function check() {
		$this->checkInstalments();
	}

	private function checkInstalments() {
		$incorrectInstalments = \DB::table('orders')
			->selectRaw('orders.id, orders.paid_amount, SUM(order_instalments.paid_amount) as paid_instalments')
			->join('order_instalments', 'orders.id', '=', 'order_instalments.order_id')
			->groupBy('orders.id')
			->havingRaw('SUM(order_instalments.paid_amount) != orders.paid_amount')
			->get();

		if ($incorrectInstalments->count() > 0) {
			$this->handleError(__METHOD__, [
				'orders.id' => $incorrectInstalments->pluck('id')->toArray(),
			]);
		}
	}
}
