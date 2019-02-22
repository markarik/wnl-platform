<?php

namespace App\Console\Commands;

use App\Models\QuizQuestion;
use App\Models\Tag;
use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class ScrapeLdekQuizQuestions extends Command
{
	protected $httpClient;

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'quiz:scrape-ldek-quiz-questions';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Scrape quiz questions from http://egzaminldek.pl/';

	public function __construct()
	{
		parent::__construct();

		$this->httpClient = new \GuzzleHttp\Client(['cookies' => true]);
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$examFormHtml = $this->login();
		$examHtml = $this->getExamHtml($examFormHtml);
		$crawler = new Crawler($examHtml);
		$questions = $crawler->filter('.pytanie');

		$answersMap = [
			'A' => 0,
			'B' => 1,
			'C' => 2,
			'D' => 3,
			'E' => 4,
		];

		$cemTaxonomy = Taxonomy::firstOrCreate(['name' => 'Klasyfikacja CEM']);
		$examTaxonomy = Taxonomy::firstOrCreate(['name' => 'Egzaminy']);

		$bar = $this->output->createProgressBar(count($questions));

		foreach($questions as $question) {
			$questionCrawler = new Crawler($question);
			$questionId = $question->getAttribute('pytanieid');

			preg_match("/var pytanieId = '$questionId'.*\\n.*?= '(.)/", $examHtml, $matches);
			$correctAnswer = $matches[1];

			$questionText = $questionCrawler->filter('.question-and-answear')->first()->html();
			$answersText = $questionCrawler->filter('.radio .question-and-answear')->each(function (Crawler $node) {
					return $node->text();
				});
			$tagsText = $questionCrawler->filter('small')->first()->text();

			$quizQuestion = QuizQuestion::firstOrCreate(['text' => $questionText], ['preserve_order'=> true]);

			foreach($answersText as $key => $answerText) {
				$quizQuestion->quizAnswers()->firstOrCreate(
					['text' => $this->transformQuizAnswerText($answerText)],
					['is_correct' => $key === ($answersMap[$correctAnswer] ?? null)]
				);
			}

			if ($tagsText) {
				$tagsNames = array_map('trim', explode(',', $tagsText));

				if ($tagsNames[0]) {
					$tag = Tag::firstOrCreate(['name' => $tagsNames[0]]);
					$term = TaxonomyTerm::firstOrCreate(['tag_id' => $tag->id, 'taxonomy_id' => $cemTaxonomy->id]);
					$quizQuestion->taxonomyTerms()->syncWithoutDetaching([$term->id]);
				}

				if ($tagsNames[1]) {
					$tag = Tag::firstOrCreate(['name' => $tagsNames[1]]);
					$term = TaxonomyTerm::firstOrCreate(['tag_id' => $tag->id, 'taxonomy_id' => $examTaxonomy->id]);
					$quizQuestion->taxonomyTerms()->syncWithoutDetaching([$term->id]);
				}
			}

			$bar->advance();
		}

		return true;
	}

	protected function login() {
		$loginPageResponse = $this->httpClient->get('http://egzaminldek.pl/login/');

		$cookies = $loginPageResponse->getHeader('Set-Cookie')[0];

		$parsedCookie = (new \GuzzleHttp\Cookie\SetCookie)->fromString($cookies);


		$examCreationFormResponse = $this->httpClient->post('http://egzaminldek.pl/login/', ['form_params' =>  [
			'username' => 'modrzas',
			'password'=> '',
			'csrfmiddlewaretoken' => $parsedCookie->getValue(),
		]]);

		return $examCreationFormResponse->getBody()->getContents();
	}

	protected function getExamHtml($examFormHtml) {
		$crawler = new Crawler($examFormHtml);

		$checkboxes = $crawler->filter('input[name="tags[]"], input[name="dzial"]');

		$examParams = [];

		foreach ($checkboxes as $checkbox) {
			$checkboxName = $checkbox->getAttribute('name');

			if (!isset($examParams[$checkboxName])) {
				$examParams[$checkboxName] = [];
			}
			$examParams[$checkboxName][] = $checkbox->getAttribute('value');
		}

		$examParams['isWebsiteSource'] = 'True';

		// Format querystring: https://stackoverflow.com/a/8171667
		$query = http_build_query($examParams, null, '&');
		$queryString = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $query);

		$examResponse = $this->httpClient->get('http://egzaminldek.pl/wygeneruj-egzamin/', ['query' => $queryString]);

		return $examResponse->getBody()->getContents();
	}

	protected function transformQuizAnswerText($text) {
		// Remove answer id prefix and dot at the end
		$text = rtrim(trim(mb_substr($text, 3)), '.');
		// mb_ucfirst
		return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
	}
}
