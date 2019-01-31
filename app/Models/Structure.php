<?php

namespace App\Models;

use App\Models\Concerns\Cached;
use Illuminate\Database\Eloquent\Model;

class Structure extends Model
{
	use Cached;

    protected $fillable = ['name', 'subject_id'];

	public function screens() {
		return $this->belongsToMany('\App\Models\Screen', 'screens');
	}

	public function sections() {
		return $this->hasMany('\App\Models\Section');
	}

	public function course()
	{
		return $this->hasOne('\App\Models\Course');
	}
}
