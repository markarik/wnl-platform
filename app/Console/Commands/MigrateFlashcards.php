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

		$this->info("All done!");
	}

	private function migrateFromLesson($lesson) {
		$openEndedQuestionsScreen = $lesson->screens()->where('name', 'like', 'Powtórki%')->first();
		if (empty($openEndedQuestionsScreen)) {
			$this->info("Lesson #{$lesson->id} does not have screen named 'Powtórki'. \n");
			return;
		}

		$dom = new \DOMDocument();
		$dom->loadHTML(
			'<head><meta http-equiv="content-type" content="text/html; charset=utf-8"/></head><body>'
			. $openEndedQuestionsScreen->content
			. '</body></html>'
		);
		$questionsElements = $dom->getElementsByTagName('ol');

		if ($questionsElements->length === 0) {
			$this->info("Lesson #{$lesson->id} does not have open-ended questions in HTML. \n");
			return;
		}

		if ($questionsElements->length > 1) {
			// handle more than one
			$body = $dom->getElementsByTagName('body')->item(0);
			$chunkedHtml = [];
			$quizSetChunk = [];
			foreach($body->childNodes as $node) {
				$quizSetChunk[] = $node;
				if ($node->nodeName === 'ol') {
					$chunkedHtml[] = $quizSetChunk;
					$quizSetChunk = [];
				}
			}
			dd($chunkedHtml);
		} else {
			$questionsRootElement = $questionsElements->index(0);
			$questionsRootElement->parentNode->removeChild($questionsRootElement);
			$body = $dom->getElementsByTagName('body')->item(0);
			$innerHtml = $this->domNodeInnerHtmlToSection($body);
			$flashcardsList = $this->flashcardsFromHtml($questionsRootElement);

			$flashcardsSet = FlashcardsSet::create([
				'description'=> $innerHtml
			]);

			foreach ($flashcardsList as $index => $flashcardRaw) {
				$flashcard = Flashcard::create($flashcardRaw);
				$flashcard->flashcardsSets()->attach($flashcardsSet, ['order_number' => $index]);
			}

			$openEndedQuestionsScreen->meta = [
				'resources' => [
					[
						'id' => $flashcardsSet->id,
						'name' => 'flashcards_sets'
					]
				]
			];
			$openEndedQuestionsScreen->type = 'flashcards';

			$openEndedQuestionsScreen->save();
		}
	}

	private function domNodeInnerHtmlToSection($domNode) {
		$innerHTML = "<div id='flashcardsDescription'>";
		$children  = $domNode->childNodes;

		foreach ($children as $child)
		{
			$innerHTML .= $domNode->ownerDocument->saveHTML($child);
		}

		$innerHTML .= "</div>";

		return $innerHTML;
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
