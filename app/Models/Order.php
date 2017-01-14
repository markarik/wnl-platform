<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'product_id',
    ];

    public function scopeRecent($query) {
        return $query
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->first();
    }

    public function product() {
        return $this->belongsTo('App\Models\Product');
    }
}
