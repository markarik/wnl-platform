<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface WithReactions
{
	/**
	 * @return MorphToMany
	 */
	public function reactions();
}
