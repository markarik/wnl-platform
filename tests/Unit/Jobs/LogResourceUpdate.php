<?php

namespace Tests\Unit\Jobs;

use App\Jobs\LogResourceUpdate;
use App\Models\QnaQuestion;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LogResourceUpdateTest extends TestCase
{
	use DatabaseTransactions;

	public function testResourceVerifiedAtUpdateLogged() {
		QnaQuestion::flushEventListeners();
		$expectedDate = Carbon::now();
		$qnaQuestion = factory(QnaQuestion::class)->create();
		$qnaQuestion->verified_at = $expectedDate;

		$job = new LogResourceUpdate($qnaQuestion, 1);
		$job->handle();

		$this->assertDatabaseHas('resource_changelog', [
			'resource_type' => QnaQuestion::class,
			'resource_id' => $qnaQuestion->id,
			'property' => 'verified_at',
			'value' => $expectedDate,
			'user_id' => 1
		]);
	}
}
