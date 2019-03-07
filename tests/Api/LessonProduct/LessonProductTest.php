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

	/** @test */
	public function only_admin_can_update_lesson_product()
	{
		$user = factory(User::class)->create();
		$product = factory(Product::class)->create();
		$lesson = factory(Lesson::class)->create();
		LessonProduct::create(['lesson_id' => $lesson->id, 'product_id' => $product->id]);

		$this
			->actingAs($user)
			->json('PUT', $this->url("/lesson_product/{$product->id}/{$lesson->id}"))
			->assertStatus(403);
	}

	/** @test */
	public function update_must_specify_start_date()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$product = factory(Product::class)->create();
		$lesson = factory(Lesson::class)->create();
		LessonProduct::create(['lesson_id' => $lesson->id, 'product_id' => $product->id]);

		$this
			->actingAs($user)
			->json('PUT', $this->url("/lesson_product/{$product->id}/{$lesson->id}"))
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
			->json('PUT', $this->url("/lesson_product/{$product->id}/{$lesson->id}"), [
				'lessons' => [
					[
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
			->json('PUT', $this->url("/lesson_product/{$nextId}/{$lesson->id}"), [
				'start_date' => '123456'
			])
			->assertStatus(404);
	}

	/** @test */
	public function only_admin_can_attach_lesson_to_product()
	{
		$user = factory(User::class)->create();
		$product = factory(Product::class)->create();
		$lesson = factory(Lesson::class)->create();
		$startDate = '654321';

		$this
			->actingAs($user)
			->json('POST', $this->url("/lesson_product/{$product->id}"), [
				'lesson_id' => $lesson->id,
				'start_date' => $startDate
			])
			->assertStatus(403);
	}

	/** @test */
	public function attach_lesson_to_product()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$product = factory(Product::class)->create();
		$lesson = factory(Lesson::class)->create();
		$startDate = '654321';

		$this
			->actingAs($user)
			->json('POST', $this->url("/lesson_product/{$product->id}"), [
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
