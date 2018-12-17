<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taggable extends Model {
	// TODO make sure this list is complete
	const PROTECTED_TAGGABLE_TYPES = [
		'App\\Models\\Lesson',
		'App\\Models\\Page',
	];
}
