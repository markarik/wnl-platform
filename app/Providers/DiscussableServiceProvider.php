<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class DiscussableServiceProvider extends ServiceProvider {
	public function register() {
		Blueprint::macro('discussable', function() {
			$this->tinyInteger('is_discussable')->default(0);
			$this->integer('discussion_id')->nullable();
		});

		Blueprint::macro('dropDiscussable', function() {
			$this->dropColumn('is_discussable');
			$this->dropColumn('discussion_id');
		});
	}
}
