<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
	protected $fillable = ['name'];

	public function scopeByName($query, $name)
	{
		return $query
			->where('name', $name)
			->first();
	}
}
