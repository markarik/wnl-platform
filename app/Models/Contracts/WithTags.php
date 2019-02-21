<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface WithTags
{
	/**
	 * @return MorphToMany
	 */
	public function tags();
}
