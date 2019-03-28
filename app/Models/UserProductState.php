<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProductState extends Model
{
	const ONBOARDING_STEP_LEARNING_STYLE = 'learning-style';
	const ONBOARDING_STEP_USER_PLAN = 'user-plan';
	const ONBOARDING_STEP_TUTORIAL = 'tutorial';
	const ONBOARDING_STEP_SATISFACTION_GUARANTEE = 'satisfaction-guarantee';
	const ONBOARDING_STEP_WELCOME = 'welcome';
	const ONBOARDING_STEP_FINAL = 'final';
	const ONBOARDING_STEP_FINISHED = 'finished';

	protected $fillable = [
		'user_id',
		'product_id',
		'onboarding_step',
	];
}
