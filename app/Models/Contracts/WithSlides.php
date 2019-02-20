<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface WithSlides
{
	/**
	 * @return MorphToMany
	 */
	public function slides();
}
