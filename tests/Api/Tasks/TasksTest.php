<?php

namespace Tests\Api\Quiz;

use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;


class TasksTest extends ApiTestCase
{

	use DatabaseTransactions;

	/** @test */
	public function get_tasks()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('moderator'));
		$task = factory(Task::class)->create();

		$response = $this
			->actingAs($user)
			->json('GET', $this->url('/tasks/' . $task->id));

		$response
			->assertStatus(200)
			->assertJson([
				'id'   => $task->id,
				'text' => $task->text,
			]);
	}

	/** @test */
	public function get_all_tasks_paginated()
	{
		$limit = 30;
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('moderator'));
		$task = factory(Task::class)->create();

		$response = $this
			->actingAs($user)
			->json('GET', $this->url('/tasks/all?limit=' . $limit));

		$response
			->assertStatus(200)
			->assertJson([
				'data'     => [],
				'per_page' => $limit,
			]);
	}

	/** @test */
	public function get_tasks_unauthorized()
	{
		$user = factory(User::class)->create();

		$response = $this
			->actingAs($user)
			->json('GET', $this->url('/tasks/all'));

		$response
			->assertStatus(403);
	}

	/** @test */
	public function patch_task()
	{
		$statusBefore = 'open';
		$statusAfter = 'resolved';
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('moderator'));
		$task = factory(Task::class)->create(['status' => $statusBefore]);
		$data = ['status' => $statusAfter];

		$response = $this
			->actingAs($user)
			->json('PATCH', $this->url('/tasks/' . $task->id), $data);

		$response
			->assertStatus(200)
			->assertJson([
				'id'     => $task->id,
				'status' => $statusAfter,
			]);
	}
}
