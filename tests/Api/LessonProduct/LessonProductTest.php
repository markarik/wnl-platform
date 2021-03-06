<?php

namespace Tests\Api\LessonProduct;

use App\Models\Lesson;
use App\Models\LessonProduct;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;


class LessonProductTest extends ApiTestCase
{
	use DatabaseTransactions;

	public function testOnlyAdminCanUpdateLessonProduct()
	{
		$user = factory(User::class)->create();
		$product = factory(Product::class)->create();
		$lesson = factory(Lesson::class)->create();
		$lessonProduct = LessonProduct::create(['lesson_id' => $lesson->id, 'product_id' => $product->id]);

		$this
			->actingAs($user)
			->json('PUT', $this->url("/lesson_product/{$lessonProduct->id}"))
			->assertStatus(403);
	}

	public function testUpdateMustSpecifyStartDate()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$product = factory(Product::class)->create();
		$lesson = factory(Lesson::class)->create();
		$lessonProduct = LessonProduct::create(['lesson_id' => $lesson->id, 'product_id' => $product->id]);

		$this
			->actingAs($user)
			->json('PUT', $this->url("/lesson_product/{$lessonProduct->id}"))
			->assertStatus(422);
	}

	public function testLessonsArrayMustContainCorrectStartDate()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$product = factory(Product::class)->create();
		$lesson = factory(Lesson::class)->create();
		$lessonProduct = LessonProduct::create(['lesson_id' => $lesson->id, 'product_id' => $product->id]);

		$this
			->actingAs($user)
			->json('PUT', $this->url("/lesson_product/{$lessonProduct->id}"), [
				'lessons' => [
					[
						'start_date' => 'hello'
					]
				]
			])
			->assertStatus(422);
	}

	public function testCanNotUpdateLessonsForNotExistingProduct()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$product = factory(Product::class)->create();
		$lesson = factory(Lesson::class)->create();
		$lessonProduct = LessonProduct::create(['lesson_id' => $lesson->id, 'product_id' => $product->id]);
		$nextId = $lessonProduct->id + 1;

		$this
			->actingAs($user)
			->json('PUT', $this->url("/lesson_product/{$nextId}"), [
				'start_date' => '123456'
			])
			->assertStatus(404);
	}

	public function testOnlyAdminCanAttachLessonToProduct()
	{
		$user = factory(User::class)->create();
		$product = factory(Product::class)->create();
		$lesson = factory(Lesson::class)->create();
		$startDate = '654321';

		$this
			->actingAs($user)
			->json('POST', $this->url("/lesson_product"), [
				'product_id' => $product->id,
				'lesson_id' => $lesson->id,
				'start_date' => $startDate
			])
			->assertStatus(403);
	}

	public function testAttachLessonToProduct()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$product = factory(Product::class)->create();
		$lesson = factory(Lesson::class)->create();
		$startDate = '654321';

		$this
			->actingAs($user)
			->json('POST', $this->url("/lesson_product"), [
				'product_id' => $product->id,
				'lesson_id' => $lesson->id,
				'start_date' => $startDate
			])
			->assertStatus(200);

		$this->assertDatabaseHas('lesson_product', [
			'lesson_id' => $lesson->id,
			'product_id' => $product->id,
			'start_date' => Carbon::createFromTimestamp($startDate)
		]);
	}

	public function testOnlyAdminCanDeleteLessonProduct()
	{
		$user = factory(User::class)->create();
		$product = factory(Product::class)->create();
		$lesson = factory(Lesson::class)->create();
		$lessonProduct = LessonProduct::create(['lesson_id' => $lesson->id, 'product_id' => $product->id]);

		$this
			->actingAs($user)
			->json('DELETE', $this->url("/lesson_product/{$lessonProduct->id})"))
			->assertStatus(403);
	}

	public function testCanNotDeleteNotExistingUserLesson()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$product = factory(Product::class)->create();
		$lesson = factory(Lesson::class)->create();
		$lessonProduct = LessonProduct::create(['lesson_id' => $lesson->id, 'product_id' => $product->id]);
		$nextId = $lessonProduct->id + 1;

		$this
			->actingAs($user)
			->json('DELETE', $this->url("/lesson_product/{$nextId}"))
			->assertStatus(404);
	}

	public function testAttachedLessonIsDeleted()
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
			->json('DELETE', $this->url("/lesson_product/{$lessonProduct->id}"))
			->assertStatus(200);

		$this->assertEmpty(LessonProduct::find($lessonProduct->id), 'lesson product is removed');
	}
}
