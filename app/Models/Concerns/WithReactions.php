<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface WithReactions
{
	/**
	 * @return MorphToMany
	 */
	public function reactions();
}
