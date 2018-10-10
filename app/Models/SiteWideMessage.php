<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteWideMessage extends Model
{
	const SITE_WIDE_ALERT_DISPLAY_TARGET  = 'site-wide-alert';
	const DASHBOARD_NEWS_DISPLAY_TARGET  = 'dashboard-news';

	protected $fillable = ['message', 'read_at', 'start_date', 'end_date', 'user_id', 'slug', 'target'];

	protected $casts = [
		'start_date' => 'date',
		'end_date'   => 'date'
	];

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}
}
