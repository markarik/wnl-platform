<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class DiscussableServiceProvider extends ServiceProvider {
	public function register() {
		Blueprint::macro('discussable', function() {
			$this->tinyInteger('discussable')->default(0);
		});

		Blueprint::macro('dropDiscussable', function() {
			$this->dropColumn('discussable');
		});
	}
}
