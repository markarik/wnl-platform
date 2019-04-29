<?php

namespace Tests\Api\Comments;

use App\Models\Comment;
use App\Models\QnaAnswer;
use App\Models\QnaQuestion;
use App\Models\Role;
use App\Models\Screen;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;


class CommentsTest extends ApiTestCase
{
	use DatabaseTransactions;

	/** @test */
	public function post_comment()
	{
		QnaAnswer::flushEventListeners();
		QnaQuestion::flushEventListeners();
		Comment::flushEventListeners();

		$user = User::find(1);

		$tag = factory(Tag::class)->create();
		$screen = factory(Screen::class)->create();
		$question = factory(QnaQuestion::class)->create();
		$answer = factory(QnaAnswer::class)->create(['question_id' => $question->id]);
		$question->tags()->attach($tag);
		$screen->tags()->attach($tag);

		$data = [
			'commentable_resource' => config('papi.resources.qna-answers'),
			'commentable_id' => $answer->id,
			'text' => 'Kolekcjonuję antarktyczne drewniane kaczki, gdyby ktoś coś miał, proszę o info na priv. Pozdrawiam.',
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/comments'), $data);

		$response
			->assertStatus(200)
			->assertJsonStructure(['id', 'text', 'created_at', 'updated_at']);
	}

	/** @test */
	public function delete_comment()
	{
		Comment::flushEventListeners();
		QnaQuestion::flushEventListeners();
		QnaAnswer::flushEventListeners();

		$user = User::find(1);
		$comment = factory(Comment::class)->create([
			'user_id' => $user->id
		]);

		// TODO: Performance issue. Problem here is we iterate over each user in DB. Not sure what to do about it yet.
		$response = $this
			->actingAs($user)
			->json('DELETE', $this->url("/comments/{$comment->id}"));

		$response
			->assertStatus(200);
	}

	public function testCommentVerifyUnverify()
	{
		Comment::flushEventListeners();
		QnaQuestion::flushEventListeners();
		QnaAnswer::flushEventListeners();

		/** @var User $user */
		$user = factory(User::class)->create();
		$moderatorRole = Role::byName('moderator');
		$user->roles()->attach($moderatorRole);

		$comment = factory(Comment::class)->create();

		$response = $this
			->actingAs($user)
			->json('GET', $this->url("/comments/{$comment->id}"));
		$response->assertStatus(200);
		$this->assertEquals(null, $response->json()['verified_at']);

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url("/comments/{$comment->id}"), [ 'verified' => true ]);
		$response->assertStatus(200);
		$this->assertNotEquals(null, $response->json()['verified_at']);

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url("/comments/{$comment->id}"), [ 'verified' => false ]);
		$response->assertStatus(200);
		$this->assertEquals(null, $response->json()['verified_at']);
	}
}
