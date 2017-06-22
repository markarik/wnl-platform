<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
	return [
		'first_name' => encrypt($faker->firstName),
		'last_name' => encrypt($faker->lastName),
		'email' => $faker->unique()->safeEmail,
		'password' => bcrypt('secret'),
		'remember_token' => str_random(10),
		'phone' => encrypt('000000000'),
		'address' => encrypt(''),
	];
});

$factory->define(App\Models\UserQuizResults::class, function (Faker\Generator $faker) {
	return [
		'user_id' => function () {
			return factory(App\Models\User::class)->create()->id;
		},
		'quiz_question_id' => function () {
			return factory(App\Models\QuizQuestion::class)->create()->id;
		},
		'quiz_answer_id' => function () {
			return factory(App\Models\QuizAnswer::class)->create()->id;
		}
	];
});

$factory->define(App\Models\QuizSet::class, function (Faker\Generator $faker) {
	return [
		'id' => $faker->randomDigitNotNull,
		'name' => $faker->name,
		'created_at' => $faker->dateTime,
		'updated_at' => $faker->dateTime
	];
});

$factory->define(App\Models\QuizQuestion::class, function (Faker\Generator $faker) {
	return [
		'id' => $faker->numberBetween(500, 1000),
		'text' => $faker->text,
		'explanation' => $faker->text,
		'preserve_order' => $faker->numberBetween(1, 20),
		'created_at' => $faker->dateTime,
		'updated_at' => $faker->dateTime
	];
});

$factory->define(App\Models\QuizAnswer::class, function (Faker\Generator $faker) {
	return [
		'id' => $faker->numberBetween(500, 1000),
		'quiz_question_id' => function () {
			return factory(App\Models\QuizQuestion::class)->create()->id;
		},
		'text' => $faker->text,
		'is_correct' => $faker->boolean(50),
		'hits' => $faker->randomDigit,
		'created_at' => $faker->dateTime,
		'updated_at' => $faker->dateTime
	];
});
