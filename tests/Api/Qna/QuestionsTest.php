<?php

namespace Tests\Api\Qna;

use App\Models\Lesson;
use App\Models\Page;
use App\Models\QnaQuestion;
use App\Models\Screen;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;


class QuestionsTest extends ApiTestCase
{
	use DatabaseTransactions;

	/** @test */
	public function post_qna_question()
	{
		$user = User::find(1);
		$tags = collect(factory(Tag::class, 3)->create())->pluck('id');

		$data = [
			'text'    => 'Meine Damen und Herren, hertzlich willkommen und nicht verstehen!',
			'tags'    => $tags,
			'context' => ['foo' => 'bar'],
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/qna_questions'), $data);

		$response
			->assertStatus(200);
	}

	/** @test * */
	public function search_qna_questions()
	{
		$user = User::find(1);

		$data = [
			'query' => [
				'hasIn' => [
					'tags' => ['tags.id', [5, 6]],
				],
			],
		];
		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/qna_questions/.search'), $data);

		$response
			->assertStatus(200);
	}

	/** @test * */
	public function search_qna_questions_by_tag_name()
	{
		$user = User::find(1);

		$data = [
			'query' => [
				'whereHas' => [
					'tags' => [
						'where' => [
							['tags.name', '=', 'Kardiologia 1'],
						],
					],
				],
				'whereIn'  => ['id', [1, 2, 3]],
			],
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/qna_questions/.search'), $data);

		$response
			->assertStatus(200);
	}

	/** @test */
	public function get_qna_question_lesson_context()
	{
		$user = factory(User::class)->create();
		$question = factory(QnaQuestion::class)->create();
		$lesson = factory(Lesson::class)->create();
		$screen = factory(Screen::class)->create([
			'lesson_id' => $lesson->id,
		]);
		$tags = factory(Tag::class, 3)->create();
		$question->tags()->attach($tags);
		$screen->tags()->attach($tags);

		$data = [
			'question_id' => $question->id,
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/qna_questions/.context'), $data);

		$response
			->assertStatus(200)
			->assertJson([
				'screen' => [
					'id' => $screen->id,
				],
				'lesson' => [
					'id' => $lesson->id,
				],
			]);
	}

	/** @test */
	public function get_qna_question_page_context()
	{
		$user = factory(User::class)->create();
		$question = factory(QnaQuestion::class)->create();
		$page = factory(Page::class)->create();
		$tags = factory(Tag::class, 3)->create();
		$question->tags()->attach($tags);
		$page->tags()->attach($tags);

		$data = [
			'question_id' => $question->id,
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/qna_questions/.context'), $data);

		$response
			->assertStatus(200)
			->assertJson([
				'page' => [
					'slug' => $page->slug,
				],
			]);
	}
}
