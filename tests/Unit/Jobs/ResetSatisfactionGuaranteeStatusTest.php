<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ResetSatisfactionGuaranteeStatus;
use App\Models\User;
use App\Models\UserSettings;
use Tests\TestCase;

class ResetSatisfactionGuaranteeStatusTest extends TestCase
{
	public function test() {
		/** @var User $user */
		$user = factory(User::class)->create([
			'has_finished_entry_exam' => true,
		]);

		factory(UserSettings::class)->create([
			'user_id' => $user->id,
			'settings' => [
				'skip_satisfaction_guarantee_modal' => true,
				'thick_scrollbar' => true,
			],
		]);

		$job = new ResetSatisfactionGuaranteeStatus($user);
		$job->handle();

		$this->assertDatabaseHas('users', [
			'id' => $user->id,
			'has_finished_entry_exam' => false,
		]);

		$this->assertDatabaseHas('user_settings', [
			'user_id' => $user->id,
			'settings' => $this->castToJson([
				'thick_scrollbar' => true,
				'skip_satisfaction_guarantee_modal' => false,
			])
		]);
	}
}
