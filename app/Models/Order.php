<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $casts = [
        'paid' => 'boolean',
    ];

    protected $fillable = [
        'user_id', 'session_id', 'product_id', 'method',
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
