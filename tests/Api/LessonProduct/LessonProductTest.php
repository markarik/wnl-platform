<?php

namespace Tests\Api\LessonProduct;

use App\Models\Lesson;
use App\Models\LessonProduct;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Tests\Api\ApiTestCase;


class LessonProductTest extends ApiTestCase
{

	/** @test */
	public function only_admin_can_update_lesson_product()
	{
		$user = factory(User::class)->create();
		$product = factory(Product::class)->create();

		$this
			->actingAs($user)
			->json('PUT', $this->url("/lesson_product/{$product->id}"))
			->assertStatus(403);
	}

	/** @test */
	public function lessons_array_must_be_set_on_input()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$product = factory(Product::class)->create();

		$this
			->actingAs($user)
			->json('PUT', $this->url("/lesson_product/{$product->id}"))
			->assertStatus(422);
	}

	/** @test */
	public function lessons_array_must_contain_existing_lesson()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$product = factory(Product::class)->create();
		$lesson = factory(Lesson::class)->create();
		$nextId = $lesson->id + 1;

		$this
			->actingAs($user)
			->json('PUT', $this->url("/lesson_product/{$product->id}"), [
				'lessons' => [
					'lesson_id' => $nextId,
					'start_date' => '111'
				]
			])
			->assertStatus(422);
	}

	/** @test */
	public function lessons_array_must_contain_correct_start_date()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$product = factory(Product::class)->create();
		$lesson = factory(Lesson::class)->create();
		LessonProduct::create(['lesson_id' => $lesson->id, 'product_id' => $product->id]);

		$this
			->actingAs($user)
			->json('PUT', $this->url("/lesson_product/{$product->id}"), [
				'lessons' => [
					[
						'lesson_id' => $lesson->id,
						'start_date' => 'hello'
					]
				]
			])
			->assertStatus(422);
	}

	/** @test */
	public function can_not_update_lessons_for_not_existing_product()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$product = factory(Product::class)->create();
		$nextId = $product->id + 1;
		$lesson = factory(Lesson::class)->create();

		$this
			->actingAs($user)
			->json('PUT', $this->url("/lesson_product/{$nextId}"), [
				'lessons' => [
					[
						'lesson_id' => $lesson->id,
						'start_date' => '123456'
					]
				]
			])
			->assertStatus(404);
	}

	/** @test */
	public function lesson_already_attached_to_product_is_updated()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$product = factory(Product::class)->create();
		$lesson = factory(Lesson::class)->create();
		$createdLessonProduct = LessonProduct::create(
			['lesson_id' => $lesson->id, 'product_id' => $product->id, 'start_date' => '123456']
		);
		$updatedStartDate = '654321';

		$this
			->actingAs($user)
			->json('PUT', $this->url("/lesson_product/{$product->id}"), [
				'lessons' => [
					[
						'lesson_id' => $lesson->id,
						'start_date' => $updatedStartDate
					]
				]
			])
			->assertStatus(200);

		$this->assertDatabaseHas('lesson_product', [
			'id' => $createdLessonProduct->id,
			'lesson_id' => $createdLessonProduct->lesson_id,
			'product_id' => $createdLessonProduct->product_id,
			'start_date' => Carbon::createFromTimestamp($updatedStartDate)
		]);
	}

	/** @test */
	public function not_attached_lesson_is_added()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$product = factory(Product::class)->create();
		$lessons = factory(Lesson::class, 2)->create();
		$createdLessonProduct = LessonProduct::create(
			['lesson_id' => $lessons[0]->id, 'product_id' => $product->id, 'start_date' => '123456']
		);
		$updatedStartDate = '654321';

		$this
			->actingAs($user)
			->json('PUT', $this->url("/lesson_product/{$product->id}"), [
				'lessons' => [
					[
						'lesson_id' => $lessons[0]->id,
						'start_date' => $updatedStartDate
					],
					[
						'lesson_id' => $lessons[1]->id,
						'start_date' => $updatedStartDate
					]
				]
			])
			->assertStatus(200);

		$this->assertDatabaseHas('lesson_product', [
			'id' => $createdLessonProduct->id,
			'lesson_id' => $createdLessonProduct->lesson_id,
			'product_id' => $createdLessonProduct->product_id,
			'start_date' => Carbon::createFromTimestamp($updatedStartDate)
		]);

		$this->assertDatabaseHas('lesson_product', [
			'id' => $createdLessonProduct->id + 1,
			'lesson_id' => $lessons[1]->id,
			'product_id' => $product->id,
			'start_date' => Carbon::createFromTimestamp($updatedStartDate)
		]);
	}

	/** @test */
	public function only_admin_can_delete_lesson_product()
	{
		$user = factory(User::class)->create();
		$product = factory(Product::class)->create();
		$lesson = factory(Lesson::class)->create();

		$this
			->actingAs($user)
			->json('DELETE', $this->url("/lesson_product/{$product->id}/{$lesson->id}"))
			->assertStatus(403);
	}

	/** @test */
	public function can_not_delete_lessons_for_not_existing_product()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$product = factory(Product::class)->create();
		$nextId = $product->id + 1;
		$lesson = factory(Lesson::class)->create();

		$this
			->actingAs($user)
			->json('DELETE', $this->url("/lesson_product/{$nextId}/{$lesson->id}"))
			->assertStatus(404);
	}

	/** @test */
	public function can_not_delete_lesson_not_attached_to_product()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$product = factory(Product::class)->create();
		$lesson = factory(Lesson::class)->create();

		$this
			->actingAs($user)
			->json('DELETE', $this->url("/lesson_product/{$product->id}/{$lesson->id}"))
			->assertStatus(404);
	}

	/** @test */
	public function can_not_delete_not_existing_lesson_from_product()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$product = factory(Product::class)->create();
		$lesson = factory(Lesson::class)->create();
		$nextId = $lesson->id + 1;

		$this
			->actingAs($user)
			->json('DELETE', $this->url("/lesson_product/{$product->id}/{$nextId}"))
			->assertStatus(404);
	}

	/** @test */
	public function attached_lesson_is_deleted()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$product = factory(Product::class)->create();
		$lesson = factory(Lesson::class)->create();
		$startDate = '123456';
		$lessonProduct = LessonProduct::create([
			'lesson_id' => $lesson->id,
			'product_id' => $product->id,
			'start_date' => $startDate
		]);

		$this
			->actingAs($user)
			->json('DELETE', $this->url("/lesson_product/{$product->id}/{$lesson->id}"))
			->assertStatus(200);

		$this->assertDatabaseMissing('lesson_product', [
			'id' => $lessonProduct->id,
			'lesson_id' => $lessonProduct->lesson_id,
			'product_id' => $lessonProduct->product_id,
			'start_date' => $startDate
		]);
	}
}
