<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tag;
use App\Models\QuizSet;
use App\Models\Lesson;

class LessonTags extends Command
{
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
	protected $description = 'Attach lesson tags to quiz questions and slides';

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

		// Attach lessons tags from Pediatria to each question from quiz sets from Pediatria
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

		// Attach lessons tags to slides
		$lessons = Lesson::all();

		foreach ($lessons as $lesson) {
			$lessonScreens = $lesson->screens;
			$tag = Tag::firstOrCreate(['name' => $lesson->name]);

			if (!$lesson->tags->contains($tag)) {
				echo("Adding $tag->name to lesson $lesson->id \n");
				$lesson->tags()->attach($tag);
			}

			foreach ($lessonScreens as $screen) {
				if ($screen->type == 'slideshow') {
					if (empty($screen->slideshow)) {
						echo("Can't tag slides from screen $screen->id from lesson $lesson->name because slideshow does't exist \n");
						continue;
					}

					$slides = $screen->slideshow->slides;
					foreach($slides as $slide) {
						if (!$slide->tags->contains($tag)) {
							echo("Adding $tag->name to slide $slide->id from screen $screen->name \n");
							$slide->tags()->attach($tag);
						}

						if (empty($slide->snippet)) {
							echo("Adding snippet to slide $slide->id from screen $screen->name \n");
							$slide->snippet = $slide->content;
							$slide->save();
						}
					}
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
			'Pediatria 10 - Stany nagłe otolaryngologia - Arkusz1.tsv' => 'P10 Otolaryngologia / Stany Nagłe',
			'Chirurgia 1 - Sheet1.tsv' => 'Wstęp / Traumatologia / Wstrząs',
			'Chirurgia 2 - Sheet1.tsv' => 'Chirurgia naczyniowa / Torakochirurgia',
			'Chirurgia 3 - Sheet1.tsv' => 'Kardio. / Uro. / Neuroch. / Plast. / Sutek / Endokryn. / Transpl.',
			'Chirurgia 4 - Sheet1.tsv' => 'Chirurgia układu pokarmowego 1',
			'Chirurgia 5 - Sheet1.tsv' => 'Chirurgia układu pokarmowego 2 / Dziecięca',
			'G2 Endokrynologia ginekologiczna - Arkusz1.tsv' => 'Ginekologia 2',
			'G3 - styczeń 2018' => 'Ginekologia 3',
			'G4 - styczeń 2018' => 'Ginekologia 4',
			'G5 - styczeń 2018' => 'Ginekologia 5'
		];
	}
}
