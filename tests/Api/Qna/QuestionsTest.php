<?php

namespace Tests\Api\Qna;

use App\Models\Discussion;
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
		QnaQuestion::flushEventListeners();
		$user = User::find(1);
		$tags = collect(factory(Tag::class, 3)->create())->pluck('id');
		$discussionId = factory(Discussion::class)->create()->id;

		$data = [
			'text'    => 'Meine Damen und Herren, hertzlich willkommen und nicht verstehen!',
			'tags'    => $tags,
			'context' => ['foo' => 'bar'],
			'discussion_id' => $discussionId
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/qna_questions'), $data);

		$response
			->assertStatus(200);
	}

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
			'context' => $question->id,
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/qna_questions/.context'), $data);

		$response
			->assertStatus(200)
			->assertJson([
				'name' => 'screens',
				'params' => [
					'screenId' => $screen->id,
					'lessonId' => $screen->lesson->id,
					'courseId' => $screen->lesson->group->course->id
				]
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
			'context' => $question->id,
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/qna_questions/.context'), $data);

		$response
			->assertStatus(200)
			->assertJson([
				'name' => $page->slug,
			]);
	}
}
