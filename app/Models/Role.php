<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Role extends Model
{
	use Notifiable;

	const ROLE_ADMIN = 'admin';
	const ROLE_MODERATOR = 'moderator';

	protected $fillable = ['name'];

	public function scopeByName($query, $name)
	{
		return $query
			->where('name', $name)
			->first();
	}
}
