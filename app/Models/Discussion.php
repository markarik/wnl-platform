<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
	protected $fillable = ['name'];

	public function questions() {
		return $this->hasMany('\App\Models\QnaQuestion', 'discussion_id');
	}

	public function screen() {
		return $this->hasOne('\App\Models\Screen', 'discussion_id');
	}

	public function page() {
		return $this->hasOne('\App\Models\Page', 'discussion_id');
	}
}
