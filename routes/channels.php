<?php


use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('active-users', function ($user) {
	// TODO AUTH ME
	return [
		'id' => $user->id,
		'profile' => [
			'avatar' => $user->profile->avatar_url,
			'city' => $user->profile->city,
			'full_name' => $user->profile->full_name,
			'help' => $user->profile->help,
			'user_id' => $user->profile->user_id,
		],
	];
});

Broadcast::channel('lesson.{lessonId}', function ($user, $lessonId) {
	return [
		'id' => $user->id,
		'profile' => [
			'avatar' => $user->profile->avatar_url,
			'city' => $user->profile->city,
			'full_name' => $user->profile->full_name,
			'help' => $user->profile->help,
			'user_id' => $user->profile->user_id,
		],
	];
});
