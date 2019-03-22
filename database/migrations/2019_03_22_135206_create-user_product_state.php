<?php

use App\Models\UserProductState;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserProductState extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
			Schema::create('user_product_states', function (Blueprint $table) {
				$table->increments('id');
				$table->unsignedInteger('user_id');
				$table->unsignedInteger('product_id');
				$table->enum('wizard_step', [
					UserProductState::WIZARD_STEP_LEARNING_STYLE,
					UserProductState::WIZARD_STEP_USER_PLAN,
					UserProductState::WIZARD_STEP_TUTORIAL,
					UserProductState::WIZARD_STEP_SATISFACTION_GUARANTEE,
					UserProductState::WIZARD_STEP_WELCOME,
					UserProductState::WIZARD_STEP_FINISHED,
				])->nullable();
				$table->timestamps();

				$table->unique(
					['user_id', 'product_id',],
					'user_product_state_unique'
				);
			});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
			Schema::dropIfExists('user_product_states');
    }
}
