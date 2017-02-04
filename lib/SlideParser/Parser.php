<?php

namespace Lib\SlideParser;

use App\Exceptions\ParseErrorException;
use App\Models\Category;
use App\Models\Lesson;
use App\Models\Slide;
use App\Models\Snippet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Parser
{

	/**
	 * Regexp patterns used to process html slide shows
	 */
	const SLIDE_PATTERN = '/<section>([\s\S]*?)<\/section>/i';

	const LESSON_PATTERN = '/#!\(lesson:(.*)\)/';

	const SUBJECT_GROUP_PATTERN = '/#!\(subject_group:(.*)\)/';

	const SUBJECT_PATTERN = '/#!\(subject:(.*)\)/';

	const SECTION_GROUP_PATTERN = '/#!\(section_group:(.*)\)/';

	const SECTION_PATTERN = '/#!\(section:(.*)\)/';

	const FUNCTIONAL_SLIDE_PATTERN = '/#!\(functional\)/';

	const TAG_PATTERN = '/#!\((.*)\)/';

	/**
	 * @var Lesson
	 */
	protected $lesson;

	/**
	 * @var Snippet
	 */
	protected $snippet;

	/**
	 * @var Category
	 */
	protected $subjectGroup;

	/**
	 * @var Model
	 */
	protected $parent;

	/**
	 * @var Category
	 */
	protected $subject;

	/**
	 * @var Category
	 */
	protected $sectionGroup;

	/**
	 * @var Category
	 */
	protected $section;

	/**
	 * Parser constructor.
	 */
	public function __construct()
	{
		Log::debug(__CLASS__ . ' called');
	}

	/**
	 * @param $data - string/html
	 */
	public function parse($data)
	{
		$slides = $this->matchSlides($data);

		foreach ($slides as $key => $content) {
			$slide = Slide::create([
				'content'       => preg_replace(self::TAG_PATTERN, '', $content),
				'is_functional' => $this->isFunctional($content),
			]);

			if ($key === 0) $this->matchSubjectGroup($content);

			$this->matchLesson($content);
			if ($this->snippet) {
				$this->snippet->slides()->attach($slide);
			}

			if ($slide->is_functional) continue; /* jump to next iteration */

			$this->matchSubject($content);
			if ($this->subject) {
				$this->subject->slides()->attach($slide);
			}

			$this->matchSectionGroup($content);
			if ($this->sectionGroup) {
				$this->sectionGroup->slides()->attach($slide);
			}

			$this->matchSection($content);
			if ($this->section) {
				$this->section->slides()->attach($slide);
			}
		}
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
	 * @param $data - string/html
	 * @throws ParseErrorException
	 */
	public function matchSubjectGroup($data)
	{
		$match = [];
		$matchingResult = preg_match(self::SUBJECT_GROUP_PATTERN, $data, $match);

		if (!$matchingResult) {
			throw new ParseErrorException('Subject group tag not found!');
		}

		$subjectGroup = Category::firstOrCreate(['name' => $match[1]]);

		$this->subjectGroup = $subjectGroup;
		$this->parent = $subjectGroup;
	}

	/**
	 * @param $data
	 * @return bool
	 */
	public function matchSubject($data)
	{
		$match = [];
		$matchingResult = preg_match(self::SUBJECT_GROUP_PATTERN, $data, $match);

		if (!$matchingResult) {
			return false;
		}

		$subject = Category::updateOrCreate(
			['name' => $match[1]],
			['name' => $match[1], 'parent_id' => $this->parent->id]
		);

		$this->subject = $subject;
		$this->parent = $subject;

		return true;
	}

	/**
	 * @param $data
	 * @return bool
	 */
	public function matchSectionGroup($data)
	{
		$match = [];
		$matchingResult = preg_match(self::SECTION_GROUP_PATTERN, $data, $match);

		if (!$matchingResult) {
			return false;
		}

		$sectionGroup = Category::updateOrCreate(
			['name' => $match[1]],
			['name' => $match[1], 'parent_id' => $this->parent->id]
		);

		$this->sectionGroup = $sectionGroup;
		$this->parent = $sectionGroup;

		return true;
	}

	/**
	 * @param $data
	 * @return bool
	 */
	public function matchSection($data)
	{
		$match = [];
		$matchingResult = preg_match(self::SECTION_PATTERN, $data, $match);

		if (!$matchingResult) {
			return false;
		}

		$section = Category::updateOrCreate(
			['name' => $match[1]],
			['name' => $match[1], 'parent_id' => $this->parent->id]
		);

		$this->section = $section;

		return true;
	}

	/**
	 * @param $data
	 * @return Lesson|bool
	 * @internal param $slide
	 */
	public function matchLesson($data)
	{
		$match = [];
		$matchingResult = preg_match(self::LESSON_PATTERN, $data, $match);

		if (!$matchingResult) {
			return false;
		}

		$this->lesson = $lesson = Lesson::create([
			'name' => $match[1],
		]);

		$this->snippet = Snippet::create(['type' => 'slideshow']);
		$lesson->screens()->create(['snippet_id' => $this->snippet->id]);

		return true;
	}
}