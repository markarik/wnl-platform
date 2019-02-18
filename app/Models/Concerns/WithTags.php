<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface WithTags
{
	/**
	 * @return MorphToMany
	 */
	public function tags();
}
