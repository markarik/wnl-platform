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
		'first_name'     => encrypt($faker->firstName),
		'last_name'      => encrypt($faker->lastName),
		'email'          => $faker->unique()->safeEmail,
		'password'       => bcrypt('secret'),
		'remember_token' => str_random(10),
		'phone'          => encrypt('000000000'),
		'address'        => encrypt(''),
	];
});

$factory->define(App\Models\UserQuizResults::class, function (Faker\Generator $faker) {
	return [
		'user_id'          => function () {
			return factory(App\Models\User::class)->create()->id;
		},
		'quiz_question_id' => function () {
			return factory(App\Models\QuizQuestion::class)->create()->id;
		},
		'quiz_answer_id'   => function () {
			return factory(App\Models\QuizAnswer::class)->create()->id;
		},
	];
});

$factory->define(App\Models\QuizSet::class, function (Faker\Generator $faker) {
	return [
		'name'       => $faker->name,
		'created_at' => $faker->dateTime,
		'updated_at' => $faker->dateTime,
	];
});

$factory->define(App\Models\QuizQuestion::class, function (Faker\Generator $faker) {
	return [
		'text'           => $faker->text,
		'explanation'    => $faker->text,
		'preserve_order' => $faker->numberBetween(1, 20),
		'created_at'     => $faker->dateTime,
		'updated_at'     => $faker->dateTime,
	];
});

$factory->define(App\Models\QuizAnswer::class, function (Faker\Generator $faker) {
	return [
		'quiz_question_id' => function () {
			return factory(App\Models\QuizQuestion::class)->create()->id;
		},
		'text'             => $faker->text,
		'is_correct'       => $faker->boolean(50),
		'hits'             => $faker->randomDigit,
		'created_at'       => $faker->dateTime,
		'updated_at'       => $faker->dateTime,
	];
});

$factory->define(App\Models\Tag::class, function (Faker\Generator $faker) {
	return [
		'name' => $faker->name,
	];
});

$factory->define(App\Models\Screen::class, function (Faker\Generator $faker) {
	return [
		'id'        => $faker->numberBetween(500, 1000),
		'name'      => $faker->name,
		'type'      => 'slideshow',
		'lesson_id' => function () {
			return factory(App\Models\Lesson::class)->create()->id;
		},
	];
});

$factory->define(App\Models\Lesson::class, function (Faker\Generator $faker) {
	return [
		'name'     => $faker->name,
		'group_id' => function () {
			return factory(App\Models\Group::class)->create()->id;
		},
	];
});

$factory->define(App\Models\Group::class, function (Faker\Generator $faker) {
	return [
		'name'      => $faker->name,
		'course_id' => function () {
			return factory(App\Models\Course::class)->create()->id;
		},
	];
});

$factory->define(App\Models\Course::class, function (Faker\Generator $faker) {
	return [
		'id'   => $faker->numberBetween(500, 1000),
		'name' => $faker->name,
		'slug' => $faker->name,
	];
});

$factory->define(App\Models\QnaQuestion::class, function (Faker\Generator $faker) {
	return [
		'text'    => $faker->text,
		'user_id' => 1,
	];
});

$factory->define(App\Models\QnaAnswer::class, function (Faker\Generator $faker) {
	return [
		'text'    => $faker->text,
		'user_id' => 1,
		'question_id' => function () {
			return factory(App\Models\QnaQuestion::class)->create()->id;
		},
	];
});

$factory->define(App\Models\ChatRoom::class, function (Faker\Generator $faker) {
	return [
		'name' => $faker->text,
	];
});

$factory->define(App\Models\Comment::class, function (Faker\Generator $faker) {
	return [
		'text'    => $faker->text,
		'user_id' => function () {
			return factory(App\Models\User::class)->create()->id;
		},
		'commentable_id' => function () {
			return factory(App\Models\QnaAnswer::class)->create()->id;
		},
		'commentable_type' => 'App\\Models\\QnaAnswer'
	];
});

$factory->define(App\Models\Slide::class, function (Faker\Generator $faker) {
	return [
		'content' => $faker->text,
	];
});
