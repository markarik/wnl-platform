<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
	protected $casts = [
		'amount' => 'float',
		'meta' => 'array',
	];

	protected $fillable = ['order_id', 'number', 'series', 'external_id',
		'amount', 'vat', 'corrected_invoice_id', 'meta', 'type'];

	public function order()
	{
		return $this->belongsTo('App\Models\Order');
	}

	public function correctives()
	{
		return $this->hasMany('App\Models\Invoice', 'corrected_invoice_id');
	}

	public function getFileNameAttribute()
	{
		return str_slug($this->id) . '.pdf';
	}

	public function getFilePathAttribute()
	{
		return 'invoices/' . $this->file_name;
	}

	public function getNumberSluggedAttribute()
	{
		return str_slug($this->full_number);
	}

	public function getFullNumberAttribute()
	{
		return $this->series . '/' . $this->number;
	}

	public function getVatRateAttribute()
	{
		if ($this->vat === 'zw') {
			return 0;
		}

		return (int) $this->vat / 100;
	}

	public function getVatAmountAttribute()
	{
		return $this->amount - $this->netValue;
	}

	public function getNetValueAttribute()
	{
		return $this->amount / (1 + $this->vatRate);
	}

	public function getCorrectedAmountAttribute()
	{
		return $this->amount + $this->correctives->sum('amount');
	}

	public function scopeRecent($query)
	{
		return $query
			->orderBy('created_at', 'desc')
			->take(1)
			->first();
	}
}
