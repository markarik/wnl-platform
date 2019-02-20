<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class DiscussableServiceProvider extends ServiceProvider {
	public function register() {
		Blueprint::macro('discussable', function() {
			// Workaround for https://github.com/nunomaduro/larastan/issues/42
			/** @var Blueprint $table */
			$table = $this;
			$table->tinyInteger('is_discussable')->default(0);
			$table->integer('discussion_id')->nullable();
		});

		Blueprint::macro('dropDiscussable', function() {
			/** @var Blueprint $table */
			$table = $this;
			$table->dropColumn('is_discussable');
			$table->dropColumn('discussion_id');
		});
	}
}
