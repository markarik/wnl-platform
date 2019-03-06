<?php

use App\Models\Tag;
use Illuminate\Database\Migrations\Migration;

class DeleteWorkshopsTag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
			$tag = Tag::where(['name' => 'Warsztaty'])->first();
			if ($tag) {
				$tag->questions()->delete();
				$tag->delete();
			}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This migration is not reversable
    }
}
