<?php

namespace Lib\SlideParser;

use App\Exceptions\ParseErrorException;
use App\Models\Category;
use App\Models\Slide;
use App\Models\Snippet;
use Illuminate\Support\Facades\Log;
use App\Models\Structure;

class Parser
{
	// TODO: Search&replace rules:
	// - Remove width/height form iframes

	/**
	 * Regexp patterns used to process html slide shows
	 */
	const SLIDE_PATTERN = '/<section>([\s\S]*?)<\/section>/i';

	const FUNCTIONAL_SLIDE_PATTERN = '/#!\(functional\)/';

	const TAG_PATTERN = '/#!\(([\w]*):([^\)]*)\)/';

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
			0 => 'subject_group',
			1 => 'subject',
			2 => 'section_group',
			3 => 'section',
		]);

		$this->courseTags = collect([
			0 => 'lesson',
		]);
	}

	/**
	 * @param $data - string/html
	 * @throws ParseErrorException
	 */
	public function parse($data)
	{
		// TODO: Unspaghettize this code
		$iteration = 0;
		$slides = $this->matchSlides($data);
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
				'content'       => preg_replace(self::TAG_PATTERN, '', $slideHtml),
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
				$this->courseModels = array_filter($this->courseModels, function ($key) use ($index) {
					return $key >= $index;
				}, ARRAY_FILTER_USE_KEY);

				if ($index === 0) {
					$parentId = null;
				} else {
					$parentId = $this->courseModels[$index - 1]->id;
				}
				$structure = Structure::firstOrCreate([
					'name'      => $courseTag['value'],
					'parent_id' => $parentId,
					'course_id' => 1,
				]);
				$this->courseModels[] = $structure->snippets()->create(['type' => 'slideshow']);
			}
			foreach ($this->courseModels as $model) {
				$model->slides()->attach($slide);
			}

			if ($slide->is_functional) continue; /* jump to next iteration */

			$foundCategoryTags = [];
			foreach ($tags as $tagName => $tagValue) {
				$searchResult = $this->categoryTags->search($tagName);
				if ($searchResult !== false) {
					$foundCategoryTags[$searchResult] = ['name' => $tagName, 'value' => $tagValue];
				}
			}
			if ($currentSlide === 0 && !array_key_exists(0, $foundCategoryTags)) {
				throw new ParseErrorException('Highest level category tag not found!');
			}
			ksort($foundCategoryTags);
			foreach ($foundCategoryTags as $index => $categoryTag) {
				if ($index === 0) {
					$parentId = null;
				} else {
					$parentId = $this->categoryModels[$index - 1]->id;
				}

				$this->categoryModels = array_filter($this->categoryModels, function ($key) use ($index) {
					return $key < $index;
				}, ARRAY_FILTER_USE_KEY);

				$this->categoryModels[] = Category::firstOrCreate([
					'name'      => $categoryTag['value'],
					'parent_id' => $parentId,
				]);

				if ($index === 0) {
					$this->categoryModels[0]->slides()->detach();
					Category::where('parent_id', $this->categoryModels[0]->id)->delete();
				}
			}

			foreach ($this->categoryModels as $model) {
				$model->slides()->attach($slide);
			}

		}
		die('kuniec');
	}

	/**
	 * @param $data - string/html
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

}