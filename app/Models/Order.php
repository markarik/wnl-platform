<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use ScoutEngines\Elasticsearch\Searchable;

class Order extends Model
{
	use Searchable;

	protected $casts = [
		'paid'        => 'boolean',
		'canceled'    => 'boolean',
		'invoice'     => 'boolean',
		'canceled_at' => 'date',
		'paid_amount' => 'float',
	];

	protected $fillable = [
		'user_id', 'session_id', 'product_id', 'method', 'transfer_title',
		'external_id', 'canceled', 'canceled_at',
		'paid_amount', 'invoice', 'paid_at',
	];

	protected $guarded = [
		'paid',
	];

	protected $dates = [
		'paid_at',
	];

	public function scopeRecent($query)
	{
		return $query
			->orderBy('created_at', 'desc')
			->take(1)
			->first();
	}

	public function product()
	{
		return $this->belongsTo('App\Models\Product');
	}

	public function coupon()
	{
		return $this->belongsTo('App\Models\Coupon');
	}

	public function studyBuddy()
	{
		return $this->hasOne('App\Models\StudyBuddy');
	}

	public function invoices()
	{
		return $this->hasMany('App\Models\Invoice');
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function orderInstalments()
	{
		return $this->hasMany('App\Models\OrderInstalment');
	}

	public function paymentReminders()
	{
		return $this->hasMany('App\Models\PaymentReminder');
	}

	public function payments() {
		return $this->hasMany('App\Models\Payment');
	}

	public function attachCoupon($coupon)
	{
		$this->coupon_id = $coupon->id;
		$this->save();
	}

	public function getTotalWithCouponAttribute()
	{
		$coupon = $this->coupon;

		if (is_null($coupon)) return $this->product->price;

		return $this->product->price - $this->coupon_amount;
	}

	public function getCouponAmountAttribute()
	{
		$coupon = $this->coupon;

		if (is_null($coupon)) return 0;

		if ($coupon->is_percentage) {
			return number_format($coupon->value * $this->product->price / 100, 2, '.', '');
		}

		return $coupon->value;
	}

	public function getInstalmentsAttribute()
	{
		if ($this->paid_amount >= $this->total_with_coupon) {
			return [
				'allPaid'     => true,
				'instalments' => [],
			];
		}

		$orderInstalments = $this->orderInstalments()->get();

		if ($orderInstalments->count() === 0) {
			$orderInstalments = $this->generatePaymentSchedule();
		}

		$nextPayment = $orderInstalments
			->sort(function (OrderInstalment $a, OrderInstalment $b) {
				return $a->order_number - $b->order_number;
			})
			->first(function (OrderInstalment $orderInstalment) {
				return $orderInstalment->paid !== true;
			});

		$totalLeft = $orderInstalments->reduce(function (float $left, OrderInstalment $orderInstalment) {
			return $left + $orderInstalment->left_amount;
		}, 0.0);

		return [
			'allPaid'     => false,
			'instalments' => $orderInstalments,
			'nextPayment' => $nextPayment,
			'total'       => $totalLeft,
		];
	}

	/**
	 * @return OrderInstalment[]|\Illuminate\Support\Collection
	 */
	public function generatePaymentSchedule()
	{
		$valueToDistribute = $this->total_with_coupon;
		$paidToDistribute = $this->paid_amount;

		return $this->product->instalments()
			->get()
			->map(function (ProductInstalment $instalment) use (&$paidToDistribute, &$valueToDistribute) {
				$orderNumber = $instalment->order_number;
				$amount = $instalment->value;

				if ($instalment->value_type === 'percentage') {
					$amount = $instalment->value * $valueToDistribute / 100;
				}

				$valueToDistribute -= $amount;

				if ($paidToDistribute >= $amount) {
					$paidAmount = $amount;
					$paidToDistribute -= $amount;
				} else {
					$paidAmount = $paidToDistribute;
					$paidToDistribute = 0;
				}

				return $this->orderInstalments()->firstOrNew(
					[
						'order_number' => $orderNumber
					],
					[
						'due_date' => $instalment->getDueDate($this),
						'amount' => $amount,
						'paid_amount' => $paidAmount,
						'order_number' => $orderNumber,
					]
				);
			});
	}

	public function generateAndSavePaymentSchedule()
	{
		$this->generatePaymentSchedule()->each(function (OrderInstalment $orderInstalment) {
			$orderInstalment->save();
		});
	}

	public function getIsOverdueAttribute()
	{
		$now = Carbon::now();
		if ($this->method === 'instalments') {
			return (bool) $this->orderInstalments()
				->whereRaw('paid_amount < amount')
				->where('due_date', '<', $now)
				->first();
		}

		return $this->created_at->diffInDays($now) > 7;
	}

	public function cancel()
	{
		$this->canceled = true;
		$this->canceled_at = Carbon::now();

		if (!is_null($this->method)) {
			$this->product->quantity++;
			$this->product->save();
		}

		$this->save();
	}

	public function paidAmountSufficient()
	{
		if ($this->method === 'instalments') {
			return
				$this->instalments['allPaid'] ||
				$this->instalments['instalments'][0]['amount'] <= $this->paid_amount;
		}

		return $this->paid_amount >= $this->total_with_coupon;
	}

	/**
	 * Get the indexable data array for the model.
	 *
	 * @return array
	 */
	public function toSearchableArray()
	{
		$data = [
			'id' => $this->id,
			'user_id' => $this->user_id,
			'product' => $this->product->name,
		];

		return $data;
	}
}
