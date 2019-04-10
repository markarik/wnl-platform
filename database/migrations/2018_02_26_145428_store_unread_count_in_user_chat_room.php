<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StoreUnreadCountInUserChatRoom extends Migration
{
	/**
	* Run the migrations.
	*
	* @return void
	*/
	public function up()
	{
		Schema::table('chat_room_user', function (Blueprint $table) {
			$table->nullableTimestamps();
			$table->bigInteger('unread_count')->default(0);
		});
	}

	/**
	* Reverse the migrations.
	*
	* @return void
	*/
	public function down()
	{
		Schema::table('chat_room_user', function (Blueprint $table) {
			$table->dropTimestamps();
			$table->dropColumn('unread_count');
		});
	}
}
