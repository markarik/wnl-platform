<?php
namespace Checks\DataIntegrity;

class InstalmentsDueDateCheck extends DataIntegrityCheck {
	public function check() {
		$incorrectInstalmentsDueDates = \DB::table('orders')
			->selectRaw('orders.id')
			->join('order_instalments', 'orders.id', '=', 'order_instalments.order_id')
			->join('products', 'orders.product_id', '=', 'products.id')
			->join('payment_method_product', 'orders.product_id', '=', 'payment_method_product.product_id')
			->join('payment_methods', 'payment_method_product.payment_method_id', '=', 'payment_methods.id')
			->whereRaw('order_instalments.due_date < products.signups_start')
			->where(['payment_methods.slug' => 'instalments'])
			->groupBy('orders.id')
			->get();

		if ($incorrectInstalmentsDueDates->count() > 0) {
			$this->handleError(__METHOD__, [
				'orders.id' => $incorrectInstalmentsDueDates->pluck('id')->toArray(),
			]);
		}
	}
}
