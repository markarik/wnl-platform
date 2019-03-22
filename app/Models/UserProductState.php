<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProductState extends Model
{
	const WIZARD_STEP_LEARNING_STYLE = 'learning_style';
	const WIZARD_STEP_USER_PLAN = 'user_plan';
	const WIZARD_STEP_TUTORIAL = 'tutorial';
	const WIZARD_STEP_SATISFACTION_GUARANTEE = 'satisfaction_guarantee';
	const WIZARD_STEP_WELCOME = 'welcome';
	const WIZARD_STEP_FINISHED = 'finished';

	protected $fillable = [
		'user_id',
		'product_id',
		'wizard_step',
	];
}
