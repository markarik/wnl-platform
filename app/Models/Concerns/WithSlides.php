<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface WithSlides
{
	/**
	 * @return MorphToMany
	 */
	public function slides();
}
