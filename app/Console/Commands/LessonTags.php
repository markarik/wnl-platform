<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tag;
use App\Models\QuizSet;

class LessonTags extends Command
{
	private static $QUIZ_SETS_TO_LESSONS_TAGS_MAP = [
	'Pediatria 1 - rozwój żywienie badanie - Arkusz1.tsv' => 'P1 Rozwój / Żywienie / Badanie',
	'Pediatria 2 - Genetyka okres noworodkowy choroby metaboliczne - Arkusz1.tsv' => 'P2 Genetyka / Ch.Metaboliczne / Ok.Noworodkowy',
	'Pediatria 3 - Pulmonologia  - Arkusz1.tsv' => 'P3 Pulmonologia',
	'Pediatria 4 - Kardiologia  - Arkusz1.tsv' => 'P4 Kardiologia',
	'Pediatria 5 - Gastroenterologia - Arkusz1.tsv' => 'P5 Gastroenterologia',
	'Pediatria 6 - Hematologia onkologia - Arkusz1.tsv' => 'P6 Hematologia / Onkologia',
	'Pediatria 7 - Układ moczowo-płciowy nefrologia  - Arkusz1.tsv' => 'P7 Układ Moczowy / Neurologia',
	'Pediatria 8 - Endokrynologia Reumatologia - Arkusz1.tsv' => 'P8 Endokrynologia / Reumatologia',
	'Pediatria 9 - Choroby zakaźne Szczepienia choroby alergiczne odporność  - Arkusz1.tsv' => 'P9 Zakaźne / Szczepienia / Alergie / Odporność',
	'Pediatria 10 - Stany nagłe otolaryngologia - Arkusz1.tsv' => 'P10 Otolaryngologia / Stany Nagłe'
	];
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'tags:fromLessons';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Attach lesson tags to quiz questions';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$names = array_keys($this->getMap());

		foreach ($names as $name) {
			$quizSet = QuizSet::where('name', $name)->first();
			$lessonTagName = $this->getLessonTagForQuizSet($name);
			$tag = Tag::firstOrCreate(['name' => $lessonTagName]);

			foreach($quizSet->questions as $question) {
				if (!$question->tags->contains($tag)) {
					echo("Adding $tag->name to question $question->id from quiz set $quizSet->name \n");
					$question->tags()->attach($tag);
				}
			}
		}
	}

	private function getLessonTagForQuizSet($key)
	{
		$map = $this->getMap();

		return $map[$key];
	}

	private function getMap() {
		return [
			'Pediatria 1 - rozwój żywienie badanie - Arkusz1.tsv' => 'P1 Rozwój / Żywienie / Badanie',
			'Pediatria 2 - Genetyka okres noworodkowy choroby metaboliczne - Arkusz1.tsv' => 'P2 Genetyka / Ch.Metaboliczne / Ok.Noworodkowy',
			'Pediatria 3 - Pulmonologia  - Arkusz1.tsv' => 'P3 Pulmonologia',
			'Pediatria 4 - Kardiologia  - Arkusz1.tsv' => 'P4 Kardiologia',
			'Pediatria 5 - Gastroenterologia - Arkusz1.tsv' => 'P5 Gastroenterologia',
			'Pediatria 6 - Hematologia onkologia - Arkusz1.tsv' => 'P6 Hematologia / Onkologia',
			'Pediatria 7 - Układ moczowo-płciowy nefrologia  - Arkusz1.tsv' => 'P7 Układ Moczowy / Neurologia',
			'Pediatria 8 - Endokrynologia Reumatologia - Arkusz1.tsv' => 'P8 Endokrynologia / Reumatologia',
			'Pediatria 9 - Choroby zakaźne Szczepienia choroby alergiczne odporność  - Arkusz1.tsv' => 'P9 Zakaźne / Szczepienia / Alergie / Odporność',
			'Pediatria 10 - Stany nagłe otolaryngologia - Arkusz1.tsv' => 'P10 Otolaryngologia / Stany Nagłe'
		];
	}
}
