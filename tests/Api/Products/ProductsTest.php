<?php

namespace Tests\Api\Products;


use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;

class ProductsTest extends ApiTestCase
{
	use DatabaseTransactions;

	private function transformProduct($product){
		return [
			'name'          => $product->name,
			'invoice_name'  => $product->invoice_name,
			'slug'          => $product->slug,
			'price'         => $product->price,
			'quantity'      => $product->quantity,
			'initial'       => $product->initial,
			'delivery_date' => $product->delivery_date->timestamp,
			'course_start'  => $product->course_start->timestamp,
			'course_end'    => $product->course_end->timestamp,
			'access_start'  => $product->access_start->timestamp,
			'access_end'    => $product->access_end->timestamp,
			'signups_start' => $product->signups_start->timestamp,
			'signups_end'   => $product->signups_end->timestamp,
			'signups_close' => $product->signups_close->timestamp,
			'vat_rate'      => $product->vat_rate,
			'vat_note'      => $product->vat_note,
		];
	}

	/** @test */
	public function get_products()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$product = factory(Product::class)->create();

		$response = $this
			->actingAs($user)
			->json('GET', $this->url('/products/' . $product->id));

		$response
			->assertStatus(200)
			->assertJson($this->transformProduct($product));
	}

	/** @test */
	public function update_product()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$product = factory(Product::class)->create();

		$updated = $this->transformProduct(factory(Product::class)->create(['slug' => null]));

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url("/products/{$product->id}"), $updated);

		$response
			->assertStatus(200)
			->assertJson($updated);
	}

	/** @test */
	public function create_product()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$productData = $this->transformProduct(factory(Product::class)->make());

		$response = $this
			->actingAs($user)
			->json('POST', $this->url("/products"), $productData);

		$response
			->assertStatus(200)
			->assertJson($productData);
	}

	/** @test */
	public function regular_user_cant_update_a_product()
	{
		$user = factory(User::class)->create();
		$product = factory(Product::class)->create();

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url("/products/{$product->id}"), ['price' => 1]);

		$response
			->assertStatus(403);
	}

	/** @test */
	public function regular_user_cant_create_a_product()
	{
		$user = factory(User::class)->create();
		$productData = $this->transformProduct(factory(Product::class)->make());

		$response = $this
			->actingAs($user)
			->json('POST', $this->url("/products"), $productData);

		$response
			->assertStatus(403);
	}
}
