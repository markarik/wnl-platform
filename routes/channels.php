<?php


use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('active-users', function ($user) {
	// TODO AUTH ME
	return [
		'id' => $user->id,
		'email' => $user->email,
		'avatar' => $user->profile->avatar_url,
		'fullName' => $user->profile->full_name,
		'profile' => [
			'avatar' => $user->profile->avatar_url,
			'city' => $user->profile->city,
			'display_name' => $user->profile->display_name,
			'first_name' => $user->profile->first_name,
			'full_name' => $user->profile->full_name,
			'help' => $user->profile->help,
			'id' => $user->profile->id,
			'last_name' => $user->profile->last_name,
			'learning_location' => $user->profile->learning_location,
			'user_id' => $user->profile->user_id,
		],
	];
});

Broadcast::channel('lesson.{lessonId}', function ($user, $lessonId) {
	return [
		'id' => $user->id,
		'email' => $user->email,
		'avatar' => $user->profile->avatar_url,
		'fullName' => $user->profile->full_name
	];
});
