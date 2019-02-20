<?php

namespace Tests\Api\Filtering;

use App\Models\QuizQuestion;
use App\Models\Role;
use App\Models\TaxonomyTerm;
use App\Models\User;
use \Tests\Api\ApiTestCase;


class TaxonomyTermsFilterTest extends ApiTestCase
{
	public function testDisabledFilter()
	{
		$user = factory(User::class)->create();
		$adminRole = Role::byName('admin');
		$user->roles()->attach($adminRole);
		$quizQuestions = collect(factory(QuizQuestion::class, 3)->create());

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/quiz_questions/.filter'), ['filters' => [
				0 => ['by_ids' => ['ids' => $quizQuestions->pluck('id')]],
			]]);

		$response
			->assertOk()
			->assertJsonCount(3, 'data');
	}

	public function testFilterNotMatching()
	{
		$user = factory(User::class)->create();
		$adminRole = Role::byName('admin');
		$user->roles()->attach($adminRole);
		$quizQuestions = collect(factory(QuizQuestion::class, 3)->create());
		$taxonomyTerm = factory(TaxonomyTerm::class)->create();

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/quiz_questions/.filter'), ['filters' => [
				0 => ['by_ids' => ['ids' => $quizQuestions->pluck('id')]],
				1 => ['taxonomy_terms'=> [$taxonomyTerm->id]],
			]]);

		$response
			->assertOk()
			->assertJsonCount(0, 'data');
	}

	public function testFilterMatching()
	{
		$user = factory(User::class)->create();
		$adminRole = Role::byName('admin');
		$user->roles()->attach($adminRole);
		$taxonomyTerm = factory(TaxonomyTerm::class)->create();
		$quizQuestions = collect(factory(QuizQuestion::class, 3)->create());
		$quizQuestions->get(0)->taxonomyTerms()->attach($taxonomyTerm);

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/quiz_questions/.filter'), ['filters' => [
				0 => ['by_ids' => ['ids' => $quizQuestions->pluck('id')]],
				1 => ['taxonomy_terms'=> [$taxonomyTerm->id]],
			]]);

		$response
			->assertOk()
			->assertJsonCount(1, 'data');
	}
}
