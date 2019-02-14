<?php

namespace Tests\Api\User;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Mail;
use Tests\Api\ApiTestCase;

class UserNotificationsTest extends ApiTestCase
{
	public function test_patch_notifications_mark_as_read()
	{
		$user = factory(User::class)->create();

		UserSubscription::create([
			'user_id'      => $user->id,
			'access_start' => Carbon::yesterday(),
			'access_end'   => Carbon::tomorrow()
		]);

		$payload = ['read_at' => time()];

		$response = $this
			->actingAs($user)
			->json('PATCH', $this->url("/users/{$user->id}/notifications"), $payload);

		$response
			->assertStatus(200);
	}

	public function test_get_notifications_for_user()
	{
		$user = factory(User::class)->create();
		$userChannel = "channel-user-{$user->id}";

		UserSubscription::create([
			'user_id'      => $user->id,
			'access_start' => Carbon::yesterday(),
			'access_end'   => Carbon::tomorrow()
		]);

		factory(Notification::class, 2)->create([
			'channel'         => 'foo',
			'created_at'      => Carbon::now()->subDays(10),
			'notifiable_id'   => $user->id,
			'notifiable_type' => User::class
		]);

		factory(Notification::class, 2)->create([
			'read_at'         => Carbon::yesterday(),
			'channel'         => $userChannel,
			'created_at'      => Carbon::now()->subDays(10),
			'notifiable_id'   => $user->id,
			'notifiable_type' => User::class
		]);

		$notificationsUnread = factory(Notification::class, 2)->create([
			'channel'         => $userChannel,
			'created_at'      => Carbon::now()->subDays(10),
			'notifiable_id'   => $user->id,
			'notifiable_type' => User::class
		]);

		$notificationsMonthOld = factory(Notification::class, 2)->create([
			'channel'         => $userChannel,
			'created_at'      => Carbon::now()->subDays(30),
			'notifiable_id'   => $user->id,
			'notifiable_type' => User::class
		]);

		$response = $this
			->actingAs($user)
			->json('POST', $this->url("/users/{$user->id}/notifications/query"), [
				'channel'    => $userChannel,
				'unread'     => true,
				'older_than' => Carbon::now()->timestamp
			]);

		$expectedResponse = $notificationsUnread
			->concat($notificationsMonthOld)
			->each(function ($notification) use ($response) {
				$response->assertJsonFragment([
					'id'      => $notification->id,
					'read_at' => $notification->read_at->timestamp ?? null,
					'seen_at' => $notification->seen_at->timestamp ?? null,
					'channel' => $notification->channel,
				]);
			});
	}
}
