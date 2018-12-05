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
use App\Models\Presentable;
use App\Jobs\SearchImportAll;
use App\Models\Screen;
use App\Models\Slide;
use App\Models\Tag;
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

		self::slideCacheForget($slide);

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
			\Artisan::call('screens:countSlides');
			dispatch(new SearchImportAll('App\\Models\\Slide'));
		}

		$this->slideCacheForget($slide);
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
		$slideId = $request->get('slideId');
		$slide = Slide::find($slideId);

		// Get all presentables
		$presentables = Presentable::where('slide_id', $slideId)->get();

		$presentablesInstances = $presentables->map(function($presentable) {
			return $presentable->presentable_type::find($presentable->presentable_id);
		});

		$this->slideCacheForget($slide);

		// Detach from each presentable
		$this->detachSlide($slide, $presentablesInstances);

		// Detach Related Models
		$slide->quizQuestions()->detach();
		$slide->comments()->delete();
		$slide->reactions()->detach();

		// Decrement order numbers
		$this->decrementOrderNumber($presentables);

		$slide->delete();

		if (!App::environment('dev')) {
			dispatch(new SearchImportAll('App\\Models\\Slide'));
			\Artisan::call('screens:countSlides');
		}

		foreach ($presentablesInstances as $presentable) {
			event(new SlideDetached($slide, $presentable));
		}

		return $this->respondOk();
	}

	public function getFromCategoryByTagName(Request $request) {
		$tagName = $request->route('tagName');
		$tag = Tag::where('name', $tagName)->first();

		if (empty($tag)) {
			return $this->respondNotFound();
		}

		$slides = Slide
			::select('slides.*')
			->whereHas('tags', function($query) use ($tag) {
				$query->where('name', $tag->name);
			})
			->whereIn('slides.id', $request->get('slideIds'))
			->where('presentables.presentable_type', 'App\\Models\\Category')
			->join('presentables', 'slides.id', '=', 'presentables.slide_id')
			->orderBy('presentables.order_number', 'asc')
			->get();

		return $this->transformAndRespond($slides);
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
		$onlyAvailable = !$user->isModerator() && !$user->isAdmin();

		Slide::savePhrase(['phrase' => $request->q, 'user_id' => $user->id]);

		$query = $this->buildQuery(
			$escapedQuery,
			$onlyAvailable,
			$user
		);

		$raw = Slide::searchRaw($query);

		return $this->respondOk($raw);
	}

	public static function slideCacheForget($slide) {
		foreach ($slide->categories as $category) {
			\Cache::forget(SlideshowBuilderApiController::key(
					sprintf(SlideshowBuilderApiController::CATEGORY_SUBKEY, $category->id)
			));
		}

		foreach ($slide->slideshow as $slideshow) {
			\Cache::forget(SlideshowBuilderApiController::key(
				sprintf(SlideshowBuilderApiController::SLIDESHOW_SUBKEY, $slideshow->id)
			));
		}

		\Cache::forget(SlideshowBuilderApiController::key(
			sprintf(SlideshowBuilderApiController::SLIDE_SUBKEY, $slide->id)
		));
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
			$usersLessons = $user->lessonsAvailability->filter(function($lesson) use ($user) {
				return $lesson->isAvailable($user);
			})->pluck('id')->toArray();

			$params['body']['query']['bool']['must'] = [
				['terms' => ['context.lesson.id' => $usersLessons]]
			];
		}

		return $params;
	}
}
