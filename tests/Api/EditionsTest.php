<?php

namespace Tests\Api;

use App\Models\Edition;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EditionsTest extends TestCase
{

	/** @test */
	public function api_returns_edition_structure()
	{
		$user = User::find(1);
		$edition = Edition::find(1);
		$course = $edition->course;

		$response = $this
			->actingAs($user)
			->json('GET', 'papi/v1/editions/1?include=groups');

		$response
			->assertStatus(200)
			->assertJson([
				'id'     => $edition->id,
				'groups' => [

				],
			]);
	}

}
