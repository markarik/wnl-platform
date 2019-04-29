<?php

use Illuminate\Database\Migrations\Migration;

class MarkContentVerified extends Migration
{
	// TODO: Update users list when it's ready!
	const USERS_TO_MARK = [374];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	$now = now();
        \App\Models\QnaAnswer::whereIn('user_id', self::USERS_TO_MARK)->update(['verified_at' => $now]);
        \App\Models\Comment::whereIn('user_id', self::USERS_TO_MARK)->update(['verified_at' => $now]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    \App\Models\QnaAnswer::whereIn('user_id', self::USERS_TO_MARK)->update(['verified_at' => NULL]);
	    \App\Models\Comment::whereIn('user_id', self::USERS_TO_MARK)->update(['verified_at' => NULL]);
    }
}
