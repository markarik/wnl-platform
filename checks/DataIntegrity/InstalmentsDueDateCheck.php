<?php
namespace Checks\DataIntegrity;

class InstalmentsDueDateCheck extends DataIntegrityCheck {
	public function check() {
		$incorrectInstalmentsDueDates = \DB::table('orders')
			->selectRaw('orders.id')
			->join('order_instalments', 'orders.id', '=', 'order_instalments.order_id')
			->join('products', 'orders.product_id', '=', 'products.id')
			->whereRaw('order_instalments.due_date < products.signups_start')
			->groupBy('orders.id')
			->get();

		if ($incorrectInstalmentsDueDates->count() > 0) {
			$this->handleError(__METHOD__, [
				'orders.id' => $incorrectInstalmentsDueDates->pluck('id')->toArray(),
			]);
		}
	}
}
