<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

class TaxonomyTermable extends Model
{
	use InsertOnDuplicateKey;

	public $timestamps = false;
}
