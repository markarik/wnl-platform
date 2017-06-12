<?php namespace Lib\SlideParser;

use App\Models\Tag;
use Storage;
use App\Models\Group;
use App\Models\Slide;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\Slideshow;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use App\Exceptions\ParseErrorException;

class Parser
{
	const SLIDE_PATTERN = '/<section([\s\S]*?)>([\s\S]*?)<\/section>/';

	const FUNCTIONAL_SLIDE_PATTERN = '/[#!]+\(functional\)/';

	const TAG_PATTERN = '/#!\(([\w]*):([^\)]*)\)/';

	const BACKGROUND_PATTERN = '/data-background-image="([^"]*)"/';

	protected $categoryTags;
	protected $courseTags;
	protected $categoryModels = [];
	protected $courseModels = [];

	/**
	 * Parser constructor.
	 */
	public function __construct()
	{
		Log::debug(__CLASS__ . ' called');

		$this->categoryTags = collect([
			0 => 'discipline',
			1 => 'group',
			2 => 'subject',
			3 => 'section',
			4 => 'subsection',
		]);

		$this->courseTags = collect([
			0 => 'group',
			1 => 'lesson',
			2 => 'section',
		]);
	}

	/**
	 * @param $fileContents - string/html
	 *
	 * @throws ParseErrorException
	 */
	public function parse($fileContents)
	{
		// TODO: Unspaghettize this code
		$iteration = 0;
		$orderNumber = 0;
		$slides = $this->matchSlides($fileContents);
		Log::debug('Parsing...');
		$names = [];
		foreach ($slides as $currentSlide => $slideHtml) {
			$iteration++;

			foreach ($this->categoryModels as $index => $model) {
				if (!in_array($model->name, $names)) {
					Log::debug("($iteration)" . str_repeat(' ', 5 - strlen($iteration)) . str_repeat('-', $index) . $model->name);
					$names[$model->name] = $model->name;
				}
			}

			$slide = Slide::create([
				'content'       => $this->cleanSlide($slideHtml),
				'is_functional' => $this->isFunctional($slideHtml),
			]);

			$tags = $this->getTags($slideHtml);

			$foundCourseTags = [];
			foreach ($tags as $tagName => $tagValue) {
				$searchResult = $this->courseTags->search($tagName);
				if ($searchResult !== false) {
					$foundCourseTags[$searchResult] = ['name' => $tagName, 'value' => $tagValue];
				}
			}
			ksort($foundCourseTags);
			foreach ($foundCourseTags as $index => $courseTag) {

				if ($courseTag['name'] == 'group') {
					$group = Group::firstOrCreate([
						'name'      => $courseTag['value'],
						'course_id' => 1,
					]);
					$this->courseModels['group'] = $group;
				}

				if ($courseTag['name'] == 'lesson') {
					$lesson = Lesson::firstOrCreate([
						'name'     => $courseTag['value'],
						'group_id' => $this->courseModels['group']->id,
					]);
					$slideshow = Slideshow::create([
						'background' => $this->getBackground($fileContents),
					]);
					$orderNumber = 0;
					$this->courseModels['slideshow'] = $slideshow;
					$this->courseModels['screen'] = $lesson->screens()->create([
						'type' => 'slideshow',
						'name' => 'Prezentacja',
						'meta' => [
							'resources' => [
								[
									'name' => config('papi.resources.slideshows'),
									'id'   => $slideshow->id,
								],
							],
						],
					]);
					$this->courseModels['screen']->tags()->attach(
						Tag::firstOrCreate(['name' => $lesson->name])
					);
					$this->courseModels['screen']->tags()->attach(
						Tag::firstOrCreate(['name' => $group->name])
					);
				}

				if ($courseTag['name'] == 'section') {
					$section = Section::firstOrCreate([
						'name'      => $this->cleanName($courseTag['value']),
						'screen_id' => $this->courseModels['screen']->id,
					]);
					$this->courseModels['section'] = $section;
				}
			}
			if (array_key_exists('slideshow', $this->courseModels)) {
				$this->courseModels['slideshow']->slides()->attach($slide, ['order_number' => $orderNumber]);
			}
			if (array_key_exists('section', $this->courseModels)) {
				$this->courseModels['section']->slides()->attach($slide, ['order_number' => $orderNumber]);
			}

			$orderNumber++;
			if ($slide->is_functional) continue; /* jump to next iteration */

//			$foundCategoryTags = [];
//			foreach ($tags as $tagName => $tagValue) {
//				$searchResult = $this->categoryTags->search($tagName);
//				if ($searchResult !== false) {
//					$foundCategoryTags[$searchResult] = ['name' => $tagName, 'value' => $tagValue];
//				}
//			}
//			if ($currentSlide === 0 && !array_key_exists(0, $foundCategoryTags)) {
//				Log::warning('Highest level category tag not found!');
//				continue;
//			}
//			ksort($foundCategoryTags);
//			foreach ($foundCategoryTags as $index => $categoryTag) {
//				if ($index === 0) {
//					$parentId = null;
//				} else {
//					$parentId = $this->categoryModels[$index - 1]->id;
//				}
//
//				$this->categoryModels = array_filter($this->categoryModels, function ($key) use ($index) {
//					return $key < $index;
//				}, ARRAY_FILTER_USE_KEY);
//
//				$this->categoryModels[] = Category::firstOrCreate([
//					'name'      => $categoryTag['value'],
//					'parent_id' => $parentId,
//				]);
//
//				if ($index === 0) {
//					$this->categoryModels[0]->slides()->detach();
//					Category::where('parent_id', $this->categoryModels[0]->id)->delete();
//				}
//			}
//
//			foreach ($this->categoryModels as $model) {
//				$model->slides()->attach($slide);
//			}

		}
		// die('kuniec');
	}

	/**
	 * @param $data - string/html
	 *
	 * @return array
	 */
	protected function matchSlides($data):array
	{
		$matches = [];

		preg_match_all(self::SLIDE_PATTERN, $data, $matches);

		return $matches[0];
	}

	/**
	 * @param $data - string/html
	 *
	 * @return bool
	 */
	protected function isFunctional($data):bool
	{
		return (bool)preg_match(self::FUNCTIONAL_SLIDE_PATTERN, $data);
	}

	/**
	 * @param $pattern
	 * @param $data
	 * @param \Closure $errback
	 *
	 * @return mixed
	 * @internal param \Closure $callback
	 */
	public function match($pattern, $data, \Closure $errback = null)
	{
		$match = [];
		$matchingResult = preg_match_all($pattern, $data, $match, PREG_SET_ORDER);

		if (!$matchingResult) {
			if (is_callable($errback)) {
				$errback();
			}

			return false;
		}

		return $match;
	}

	/**
	 * @param $slideHtml
	 *
	 * @return array
	 */
	public function getTags($slideHtml)
	{
		$tags = [];
		$matches = $this->match(self::TAG_PATTERN, $slideHtml);

		if (!$matches) return [];

		foreach ($matches as $match) {
			$tags[$match[1]] = $match[2];
		}

		return $tags;
	}

	public function getBackground($html)
	{
		$match = $this->match(self::BACKGROUND_PATTERN, $html, function () {
			throw new ParseErrorException('No background attribute found.');
		});

		$url = $match[0][1];

		$canvas = Image::canvas(1920, 1080, '#fff');

		$background = Image::make($url)->resize(1920, 1080);

		$image = $canvas->insert($background)->stream('jpg', 80);

		$fileName = Str::random(40) . '.jpg';

		$path = 'public/backgrounds/' . $fileName;

		Storage::put($path, $image, 'public');

		return $fileName;
	}

	public function cleanSlide($html)
	{
		$regexSearch = [
			self::TAG_PATTERN,
			self::FUNCTIONAL_SLIDE_PATTERN,
			self::BACKGROUND_PATTERN,
		];

		$html = preg_replace($regexSearch, '', $html);

		return $html;
	}

	public function cleanName($name)
	{
		return strip_tags($name);
	}

}
