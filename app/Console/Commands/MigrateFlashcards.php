<?php

namespace App\Console\Commands;

use App\Models\Flashcard;
use App\Models\FlashcardsSet;
use App\Models\Lesson;
use Illuminate\Console\Command;

class MigrateFlashcards extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'flashcards:migrate {--lesson-id=}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Move all open-ended questions from HTML to Flashcards table';

	/**
	 * Create a new command instance.
	 *
	 */
	public function __construct()
	{
		parent::__construct();
	}

	const OPENING_HTML = "<p><span style=\"background-color: transparent;\">Pamiętaj, że pytania ułożone są w takiej kolejności jak zagadnienia pojawiały się na prezentacjach z danej lekcji. &nbsp;Dzięki temu łatwo będzie Ci odnaleźć pożądane informacje w razie potrzeby. Oczywiście, możesz także w tym celu użyć wyszukiwarki, jednak najlepsze efekty z powtórek osiągniesz pracując z albumem map myśli.</span></p><p><span style=\"background-color: transparent;\">Na każde z otwartych pytań staramy sobie odpowiedzieć własnymi słowami lub po prostu uświadomić sobie, że zagadnienie jest nam znane.</span></p><p><span style=\"background-color: transparent;\">Jeśli jednak tak nie jest, to wracamy do notatek lub prezentacji. :)</span></p><p>&nbsp;</p><p><span style=\"background-color: transparent;\">Dodatkowo czeka na Ciebie lista z numerami map, których dotyczą pytania i z którymi sugerujemy Ci aby w dniu dzisiejszym popracować, przejrzeć te mapy, przypomnij sobie historię, rzuć okiem gdzie na mapach umieszczone zostały poszczególne informacje. Staraj się poświęcić maksymalnie 2-3 minuty na jedną mapę.</span></p><p><span style=\"background-color: transparent;\">Powodzenia! :)</span></p>";

	const SCREEN_TO_LESSONS_MAP = [
		20 => [1],
		24 => [2],
		554 => [3],
		560 => [76],
		28 => [1, 77],
		32 => [4, 2],
		678 => [5, 3],
		36 => [84, 76],
		40 => [6, 77],
		44 => [7, 4],
		48 => [8, 5],
		527 => [9, 84],
		566 => [73, 6],
		56 => [78, 7],
		60 => [11, 8],
		534 => [12, 9],
		572 => [74, 73],
		541 => [79, 78],
		72 => [75, 73, 78],
		73 => [1, 2, 3, 76, 77, 4, 5, 84],
		74 => [6, 7, 8, 9],
		75 => [11, 12, 74, 79],
		150 => [1, 2, 3],
		156 => [18, 76, 77],
		162 => [19, 4, 5, 84],
		168 => [20, 6, 7],
		174 => [21, 8, 9],
		180 => [22, 18, 73],
		186 => [23, 19, 78],
		192 => [24, 20, 11],
		198 => [25, 21, 12],
		204 => [26, 22, 74],
		292 => [18, 19, 20, 21, 22, 23, 24, 25, 26, 27],
		218 => [27, 23, 79],
		224 => [28, 24, 75],
		230 => [29, 25],
		236 => [30, 26],
		242 => [31, 27],
		551 => [32],
		293 => [28, 29, 2, 30, 31, 32],
		256 => [28, 18],
		263 => [19, 29, 33],
		269 => [20, 30, 34],
		275 => [21, 31, 35],
		281 => [22, 32, 36],
		667 => [37],
		689 => [33, 34, 35, 36, 37, 83],
		296 => [23, 33, 83],
		302 => [24, 34, 38],
		308 => [25, 35, 39],
		314 => [26, 36, 40],
		320 => [27, 37, 41],
		331 => [38, 39, 40, 41, 42],
		333 => [28, 83, 38, 42],
		339 => [43, 29, 39],
		345 => [44, 30, 40],
		351 => [45, 31, 41],
		357 => [46, 32, 42],
		366 => [43, 44, 45, 46, 47],
		372 => [43, 47, 33],
		379 => [44, 34],
		386 => [45, 35],
		394 => [46, 36],
		400 => [47, 37, 83],
		411 => [1, 2, 3, 76, 77, 4, 5, 84],
		419 => [6, 7, 8, 9],
		426 => [11, 12, 74, 79],
		431 => [75, 73, 78],
		455 => [18, 19, 20, 21, 22, 23, 24, 25, 26, 27],
		461 => [28, 29, 2, 30, 31, 32],
		471 => [33, 34, 35, 36, 37, 83],
		481 => [38, 39, 40, 41, 42],
		487 => [43, 44, 45, 46, 47]
	];

	const LESSON_TO_ALBUM = [
		1 => '8/9/10',
		2 => '11/12',
		3 => '13/14',
		76 => '15/16/17',
		77 => '18/19',
		4 => '20/119',
		5 => '20/21/22/24',
		84 => '23/24/25',
		6 => '26/27/28/29',
		7 => '30/31/32/33',
		8 => '34/35/36/37',
		9 => '38/39/40',
		73 => '41',
		78 => '41',
		11 => '42/43/44/45',
		12 => '46/47/48/49',
		74 => '50/51/52',
		79 => '50/51/52/53/54',
		75 => '55',
		18 => '57/58',
		19 => '59/60',
		20 => '61',
		21 => '62',
		22 => '63',
		23 => '64/65',
		24 => '66/67',
		25 => '68/70',
		26 => '69/71',
		27 => '72',
		28 => '73/74',
		29 => '75/76',
		30 => '77/78',
		31 => '79/80/81/83',
		32 => '82/84',
		33 => '85/86/87/92/93',
		34 => '88/89/90/92',
		35 => '94/96/99',
		36 => '95',
		37 => '97',
		83 => '91/98',
		38 => '100',
		39 => '101/102',
		40 => '103/104',
		41 => '105/106',
		42 => '107/108/109',
		43 => '110',
		44 => '111',
		45 => '112/113',
		46 => '114/115/116/117',
		47 => '114/115/116/117',
	];

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$lessonId = $this->option('lesson-id');
		if (!empty($lessonId)) {
			$lesson = Lesson::find($lessonId);
			if (empty($lesson)) {
				$this->error("Lesson with id {$lessonId} does not exist. Try different value.");
				return;
			}
			$this->migrateFromLesson($lesson);
		} else {
			$lessons = Lesson::all();
			$bar = $this->output->createProgressBar($lessons->count());
			foreach($lessons as $lesson) {
				$this->migrateFromLesson($lesson);
				$bar->advance();
			}
			$bar->finish();
		}

		$this->info("\n All done! \n");
	}

	private function migrateFromLesson($lesson) {
		$openEndedQuestionsScreens = $lesson->screens()->where('name', 'like', 'Powtórk%')->get();
		if ($openEndedQuestionsScreens->count() === 0) {
			$this->info("\n Lesson #{$lesson->id} does not have screen named 'Powtórki'. \n");
			return;
		}

		foreach ($openEndedQuestionsScreens as $openEndedQuestionsScreen) {
			$this->migrateScreen($openEndedQuestionsScreen);
		}
	}

	private function migrateScreen($screen) {
		$dom = new \DOMDocument();
		$dom->loadHTML(
			'<head><meta http-equiv="content-type" content="text/html; charset=utf-8"/></head><body>'
			. $screen->content
			. '</body></html>'
		);
		$questionsElements = $dom->getElementsByTagName('ol');

		if ($questionsElements->length === 0) {
			$this->info("\n Screen #{$screen->id} does not have open-ended questions in HTML. \n");
			return;
		}

		$screenFlashcardsSets = [];
		$screen->content = self::OPENING_HTML;

		foreach ($questionsElements as $index => $questionElement) {
			$flashcardsList = $this->flashcardsFromHtml($questionElement);
			$flashcardsSet = new FlashcardsSet();

			$relatedLesson = self::SCREEN_TO_LESSONS_MAP[$screen->id][$index];
			$flashcardsSet->mind_maps_text = self::LESSON_TO_ALBUM[$relatedLesson];

			$flashcardsSet->lesson()->associate($relatedLesson);
			$flashcardsSet->save();

			foreach ($flashcardsList as $index => $flashcardRaw) {
				$flashcard = Flashcard::create($flashcardRaw);
				$flashcard->flashcardsSets()->attach($flashcardsSet, ['order_number' => $index]);
			}

			$screenFlashcardsSets[] = [
				'id' => $flashcardsSet->id,
				'name' => 'flashcards_sets'
			];
		}

		$screen->meta = [
			'resources' => $screenFlashcardsSets
		];
		$screen->type = 'flashcards';
		$screen->save();
	}

	private function flashcardsFromHtml($listNode) {
		$children = $listNode->childNodes;
		$flashcards = [];

		foreach ($children as $child) {
			$flashcards[] = ['content' => $child->textContent];
		}

		return $flashcards;
	}
}
