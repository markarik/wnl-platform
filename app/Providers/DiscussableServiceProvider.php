<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class DiscussableServiceProvider extends ServiceProvider {
	public function register() {
		Blueprint::macro('discussable', function() {
			/** @var mixed */
			$table = $this;
			$table->tinyInteger('is_discussable')->default(0);
			$table->integer('discussion_id')->nullable();
		});

		Blueprint::macro('dropDiscussable', function() {
			/** @var mixed */
			$table = $this;
			$table->dropColumn('is_discussable');
			$table->dropColumn('discussion_id');
		});
	}
}
