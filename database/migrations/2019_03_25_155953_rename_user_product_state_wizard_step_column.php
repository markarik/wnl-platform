<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameUserProductStateWizardStepColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_product_states', function (Blueprint $table) {
        	$table->dropColumn('wizard_step');
					$table->enum('onboarding_step', [
						'learning-style',
						'user-plan',
						'tutorial',
						'satisfaction-guarantee',
						'welcome',
						'finished',
						'final',
					])->nullable();
				});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
			Schema::table('user_product_states', function (Blueprint $table) {
				$table->dropColumn('onboarding_step');
				$table->enum('wizard_step', [
					'learning_style',
					'user_plan',
					'tutorial',
					'satisfaction_guarantee',
					'welcome',
					'finished',
				])->nullable();
			});
    }
}
