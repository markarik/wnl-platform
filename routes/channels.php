<?php


use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('active-users', function ($user) {
	// TODO AUTH ME
	return [
		'id' => $user->id,
		'email' => $user->email,
		'avatar' => $user->profile->avatar_url,
		'fullName' => $user->profile->full_name
	];
});

Broadcast::channel('lesson.{$lessonId}', function ($user, $lessonId) {
	// TODO AUTH ME
	return [
		'id' => $user->id,
		'email' => $user->email,
		'avatar' => $user->profile->avatar_url,
		'fullName' => $user->profile->full_name
	];
});
