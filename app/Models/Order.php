<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	protected $casts = [
		'paid'               => 'boolean',
		'invoice'            => 'boolean',
		'consent_newsletter' => 'boolean',
	];

	protected $fillable = [
		'user_id', 'session_id', 'product_id', 'method', 'transfer_title', 'external_id', 'invoice',
		'invoice_name', 'invoice_nip', 'invoice_address', 'invoice_zip', 'invoice_city', 'invoice_country',
		'consent_newsletter',

	];

	protected $guarded = [
		'paid',
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
}
