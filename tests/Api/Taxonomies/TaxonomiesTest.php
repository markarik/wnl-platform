<?php

namespace Tests\Api\Quiz;

use App\Models\Role;
use App\Models\Taxonomy;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;


class TaxonomiesTest extends ApiTestCase
{

	use DatabaseTransactions;

	/** @test */
	public function create_taxonomy()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));

		$taxonomyName = 'foo';
		$taxonomyDescription = 'bar';
		$taxonomyColor = '#fff';

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/taxonomies'), [
				'name' => $taxonomyName,
				'description' => $taxonomyDescription,
				'color' => $taxonomyColor
			]);

		$response
			->assertStatus(200)
			->assertJsonFragment([
				'name' => $taxonomyName,
				'description' => $taxonomyDescription,
				'color' => $taxonomyColor
			]);
	}

	/** @test */
	public function create_taxonomy_validate_name_taken()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$taxonomy = Taxonomy::create(['name' => 'foo']);

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/taxonomies'), [
				'name' => $taxonomy->name,
				'description' => 'bar',
				'color' => '#fff'
			]);

		$response
			->assertStatus(422);
	}

	/** @test */
	public function update_taxonomy()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$taxonomy = Taxonomy::create(['name' => 'foo']);
		$taxonomyDescription = 'bar';

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url('/taxonomies/' . $taxonomy->id), [
				'name' => $taxonomy->name,
				'description' => $taxonomyDescription,
			]);

		$response
			->assertStatus(200)
			->assertJson([
				'name' => $taxonomy->name,
				'id' => $taxonomy->id,
				'description' => $taxonomyDescription,
				'color' => $taxonomy->color
			]);
	}

	/** @test */
	public function update_taxonomy_name_taken()
	{
		$user = factory(User::class)->create();
		$user->roles()->attach(Role::byName('admin'));
		$taxonomy = Taxonomy::create(['name' => 'foo']);
		$taxonomyNameTaken = Taxonomy::create(['name' => 'fizz']);

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url('/taxonomies/' . $taxonomy->id), [
				'name' => $taxonomyNameTaken->name
			]);

		$response
			->assertStatus(422);
	}
}
