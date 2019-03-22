<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProductState extends Model
{
	const WIZARD_STEP_LEARNING_STYLE = 'learning-style';
	const WIZARD_STEP_USER_PLAN = 'user-plan';
	const WIZARD_STEP_TUTORIAL = 'tutorial';
	const WIZARD_STEP_SATISFACTION_GUARANTEE = 'satisfaction-guarantee';
	const WIZARD_STEP_WELCOME = 'welcome';
	const WIZARD_STEP_FINAL = 'final';
	const WIZARD_STEP_FINISHED = 'finished';

	protected $fillable = [
		'user_id',
		'product_id',
		'wizard_step',
	];
}
