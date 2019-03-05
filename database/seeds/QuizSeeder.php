<?php

use Illuminate\Database\Seeder;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use App\Models\QuizSet;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('quiz_sets')->insert([
            'name' => 'Example quiz set',
            'lesson_id' => 1,
            'description' => 'Example description',
        ]);

        \DB::table('quiz_questions')->insert([
            'text' => 'Więcej niż jedno zwierze to:',
            'preserve_order' => 1,
        ]);

        \DB::table('quiz_question_quiz_set')->insert([
            'quiz_question_id' => 1,
            'quiz_set_id' => 1,
        ]);

        \DB::table('quiz_answers')->insert([
            [
                'text' => 'Lama',
                'is_correct' => 0,
                'quiz_question_id' => 1,
            ],
            [
                'text' => 'Owca',
                'is_correct' => 0,
                'quiz_question_id' => 1,
            ],
            [
                'text' => 'Pomidor',
                'is_correct' => 0,
                'quiz_question_id' => 1,
            ],
            [
                'text' => 'Saksofon',
                'is_correct' => 1,
                'quiz_question_id' => 1,
            ],
        ]);
    }
}
