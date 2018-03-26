<?php namespace App\Http\Controllers\Api\PrivateApi;


use App;
use App\Events\Slides\SlideAdded;
use App\Events\Slides\SlideDetached;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Concerns\Slides\AddsSlides;
use App\Http\Controllers\Api\Concerns\Slides\RemovesSlides;
use App\Http\Controllers\Api\Transformers\SlideTransformer;
use App\Http\Requests\Course\DetachSlide;
use App\Http\Requests\Course\PostSlide;
use App\Http\Requests\Course\UpdateSlide;
use App\Http\Requests\Course\UpdateSlideChart;
use ScoutEngines\Elasticsearch\Searchable;
use App\Jobs\SearchImportAll;
use App\Models\Screen;
use App\Models\Slide;
use Auth;
use Artisan;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;
use Lib\SlideParser\Parser;

class SlidesApiController extends ApiController
{
	use AddsSlides, RemovesSlides;

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.slides');
	}

	public function put(UpdateSlide $request)
	{
		$slide = Slide::find($request->route('id'));

		if (!$slide) {
			return $this->respondNotFound();
		}

		$content = $request->get('content');
		$isFunctional = $request->get('is_functional');

		$parser = new Parser;
		$content = $parser->handleCharts($content);
		$content = $parser->handleImages($content);

		$slide->update([
			'content'       => $content,
			'is_functional' => $isFunctional,
		]);

		$resource = new Item($slide, new SlideTransformer, $this->resourceName);
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	/**
	 * @param PostSlide $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function post(PostSlide $request)
	{
		$screen = Screen::find($request->screen);
		$content = $request->get('content');
		$slideshow = $screen->slideshow;
		$orderNumber = $request->order_number - 1; // https://goo.gl/ZzMWT3

		$currentSlide = $this->getCurrentFromPresentables($slideshow->id, $orderNumber);
		$presentables = $this->getSlidePresentables($currentSlide, $screen);

		$presentables->push($slideshow);
		$presentables = $this->getPresentablesOrder($currentSlide, $presentables);

		$this->incrementOrderNumber($presentables);

		$parser = new Parser;
		$content = $parser->handleCharts($content);
		$content = $parser->handleImages($content);

		$slide = Slide::create([
			'is_functional' => empty($request->is_functional) ? false : true,
			'content'       => $content,
		]);

		$this->attachSlide($slide, $presentables);
		$slide->tags()->attach($screen->tags);

		if (!App::environment('dev')) {
			dispatch(new SearchImportAll('App\\Models\\Slide'));
			\Artisan::queue('screens:countSlides');
			\Artisan::queue('slides:fromCategory');
			\Artisan::call('cache:tag', ['tag' => 'presentables,slides']);
		}
		event(new SlideAdded($slide, $presentables));

		return $this->respondOk();
	}

	public function updateCharts(UpdateSlideChart $request)
	{
		$id = $request->route('slideId');
		$slide = Slide::find($id);
		if (!$slide) {
			return $this->respondNotFound();
		}

		Artisan::call('charts:update', ['id' => $id]);

		$resource = new Item($slide->fresh(), new SlideTransformer, $this->resourceName);
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function detach(DetachSlide $request)
	{
		$slide = Slide::find($request->get('slideId'));
		$screen = Screen::find($request->get('screenId'));
		$slideshow = $screen->slideshow ?? null;
		if (!$slide || !$screen || !$slideshow) {
			return $this->respondInvalidInput();
		}

		$orderNumber = $this->getSlideOrderNumber($slide, $slideshow);
		$currentSlide = $this->getCurrentFromPresentables($slideshow->id, $orderNumber);
		$presentables = $this->getSlidePresentables($currentSlide, $screen);

		$presentables->push($slideshow);
		$presentables = $this->getPresentablesOrder($currentSlide, $presentables);

		$this->decrementOrderNumber($presentables);
		$this->detachSlide($slide, $presentables);
		$slide->reactions()->detach();

		if (!App::environment('dev')) {
			dispatch(new SearchImportAll('App\\Models\\Slide'));
			\Artisan::queue('screens:countSlides');
			\Artisan::call('cache:tag', ['tag' => 'presentables,slides']);
		}
		event(new SlideDetached($slide, $presentables));

		return $this->respondOk();
	}

	public function search(Request $request) {
		$resource = $request->route('resource');

		if (!$this->canSearch(Slide::class)) {
			return $this->respondNotImplemented();
		}

		if (empty($request->q)) {
			return $this->respondInvalidInput('query not set');
		}

		$escapedQuery = $this->escapeQuery($request->q);

		if (empty(trim($escapedQuery))) {
			return $this->respondInvalidInput('query value not supported');
		}

		$user = Auth::user();
		$onlyAvailable = !$user->hasRole('moderator') && !$user->hasRole('admin');

		Slide::savePhrase(['phrase' => $request->q, 'user_id' => $user->id]);

		$query = $this->buildQuery(
			$escapedQuery,
			$onlyAvailable,
			$user
		);

		$raw = Slide::searchRaw($query);

		return $this->respondOk($raw);
	}

	protected function buildQuery($query, $onlyAvailable, $user)
	{
		// Right now it's tightly coupled with slides
		// next step - decouple
		$params = [
			'body' => [
				'from'      => 0,
				'size'      => 32,
				'query'     => [
					'bool' => [
						'should' => [
							["constant_score" => [
								'query' => ['match_phrase' => [
									'snippet.header' => [
										"query" => $query,
									],
								],
								],
								'boost' => 100,
							],
							],
							["constant_score" => [
								'query' => ['match_phrase' => [
									'snippet.content' => [
										"query" => $query,
									],
								]],
								'boost' => 90,
							],
							],
							["constant_score" => [
								'query' => ['match_phrase_prefix' => [
									'snippet.header' => [
										"query" => $query,
									],
								]],
								'boost' => 10,
							],
							],
							["constant_score" => [
								'query' => ['match_phrase_prefix' => [
									'snippet.content' => [
										"query" => $query,
									],
								]],
								'boost' => 9,
							],
							],
							['multi_match' => [
								"query"  => $query,
								'fields' => ['snippet.content', 'snippet.header^5'],
								"type"   => "cross_fields",
							]],
							['query_string' => [
								"query"            => $query,
								'fields'           => ['snippet.content'],
								"default_operator" => "and",
							]],
							['query_string' => [
								'query'            => "*{$query}*",
								'analyze_wildcard' => true,
								'fields'           => ['snippet.header', 'snippet.content'],
							]],
						],
					],
				],
				'highlight' => [
					'fields' => [
						'snippet.content' => [
							'fragment_size' => 5000,
						],
						'snippet.header'  => [
							'fragment_size' => 5000,
						],
					],
				],
			],
		];

		if (!preg_match('/\s/', $query)) {
			$params['body']['query']['bool']['should'][] = [
				'query_string' => [
					'query'            => "{$query}~",
					'analyze_wildcard' => true,
					'fields'           => ['snippet.header^5', 'snippet.content'],
				],
			];
		}

		if ($onlyAvailable) {
			$usersLessons = $user->lessonsAvailability->filter(function($lesson) {
				return $lesson->isAvailable();
			});

			foreach($usersLessons as $lesson) {
				$params['body']['query']['bool']['filter'][] = [
					'term' => ['context.lesson.id' => $lesson->id],
				];
			}
		}

		return $params;
	}
}
