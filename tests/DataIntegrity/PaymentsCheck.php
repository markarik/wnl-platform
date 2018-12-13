<?php
namespace Tests\DataIntegrity;

class PaymentsCheck extends DataIntegrityTestCase {
	public function check() {
		$orders = \DB::table('orders')
			->select(['id', 'paid_amount'])
			->where('canceled', 0)
			->get();

		foreach ($orders as $order) {
			$this->checkInstalments($order);
			$this->checkInvoices($order);
			$this->checkPayments($order);
		}
	}

	private function checkInstalments($order) {
		$incorrectInstalments = \DB::table('order_instalments')
			->selectRaw('order_id, sum(paid_amount) as sum')
			->where('order_id', $order->id)
			->groupBy('order_id')
			->having('sum', '<>', $order->paid_amount)
			->first();

		if (!empty($incorrectInstalments)) {
			$this->handleError(__METHOD__, [
				'order.id' => $order->id,
				'order.paid_amount' => $order->paid_amount,
				'instalments.sum(paid_amount)' => $incorrectInstalments->sum
			]);
		}
	}

	private function checkInvoices($order) {
		$incorrectInvoices = \DB::table('invoices')
			->selectRaw('order_id, sum(amount) as sum')
			->where('order_id', $order->id)
			->groupBy('order_id')
			->having('sum', '<>', $order->paid_amount)
			->first();

		if (!empty($incorrectInvoices)) {
			$this->handleError(__METHOD__, [
				'order.id' => $order->id,
				'order.paid_amount' => $order->paid_amount,
				'invoices.sum(amount)' => $incorrectInvoices->sum
			]);
		}
	}

	private function checkPayments($order) {
		$incorrectPayments = \DB::table('payments')
			->selectRaw('order_id, sum(amount) as sum')
			->where('order_id', $order->id)
			->groupBy('order_id')
			->having('sum', '<>', $order->paid_amount)
			->first();

		if (!empty($incorrectPayments)) {
			$this->handleError(__METHOD__, [
				'order.id' => $order->id,
				'order.paid_amount' => $order->paid_amount,
				'payments.sum(amount)' => $incorrectPayments->sum
			]);
		}
	}
}
