<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToInvoices extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('invoices', function (Blueprint $table) {
			$table->enum('type',
				[
					'corrective',
					'advance',
					'final',
					'pro-forma',
					'vat',
				]
			)->after('series');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('invoices', function (Blueprint $table) {
			$table->dropColumn('type');
		});
	}
}
