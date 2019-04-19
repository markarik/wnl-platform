<?php

namespace Tests\Api\Qna;

use App\Jobs\LogResourceUpdate;
use App\Models\Discussion;
use App\Models\Lesson;
use App\Models\Page;
use App\Models\QnaQuestion;
use App\Models\Role;
use App\Models\Screen;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Facades\App\Contracts\CourseProvider;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
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
		QnaQuestion::flushEventListeners();
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
					'courseId' => CourseProvider::getCourseId()
				]
			]);
	}

	/** @test */
	public function get_qna_question_page_context()
	{
		QnaQuestion::flushEventListeners();
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

	/** @test */
	public function verify_qna_question() {
		Bus::fake();

		$expectedDate = Carbon::create(1990, 12, 07, 12, 00, 00);
		Carbon::setTestNow($expectedDate);

		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));

		$question = factory(QnaQuestion::class)->create();

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url('/qna_questions/' . $question->id), [
				'verified' => true
			]);

		$response
			->assertStatus(200);

		$this->assertEquals($question->fresh()->verified_at, $expectedDate);
		Bus::assertDispatched(LogResourceUpdate::class);
	}
}
