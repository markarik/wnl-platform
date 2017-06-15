<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reactable extends Model
{
	protected $fillable = ['user_id', 'reaction_id', 'reactable_id', 'reactable_type'];
}
