<?php


use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('active-users', function ($user) {
	return [
		'id' => $user->id,
		'email' => $user->email,
	];
});
