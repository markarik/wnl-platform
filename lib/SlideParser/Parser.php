<?php

namespace Lib\SlideParser;

use App\Exceptions\ParseErrorException;
use App\Models\Lesson;
use App\Models\Slide;
use App\Models\Snippet;
use App\Models\Subject;
use Illuminate\Support\Facades\Log;

class Parser
{

	/**
	 * Regexp patterns used to process html slide shows
	 */
	const SLIDE_PATTERN = '/<section>([\s\S]*?)<\/section>/i';

	const SUBJECT_PATTERN = '/#!\(subject:(.*)\)/';

	const LESSON_PATTERN = '/#!\(lesson:(.*)\)/';

	const SECTION_PATTERN = '/#!\(section:(.*)\)/';

	const FUNCTIONAL_SLIDE_PATTERN = '/#!\(functional\)/';

	/**
	 * @var Subject
	 */
	protected $subject;


	/**
	 * @var Lesson
	 */
	protected $lesson;

	/**
	 * @var Snippet
	 */
	protected $snippet;

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

		foreach ($slides as $key => $content)
		{
			$slide = Slide::create([
				'content'       => $content,
				'is_functional' => $this->isFunctional($content),
			]);

			if ($key === 0) $this->matchSubject($content);

			$this->matchLesson($content);
			$this->matchSection($content, $slide);

			$this->subject->slides()->attach($slide);
			$this->snippet->slides()->attach($slide);
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
	public function matchSubject($data)
	{
		$match = [];
		$matchingResult = preg_match(self::SUBJECT_PATTERN, $data, $match);

		if (!$matchingResult) {
			throw new ParseErrorException('Subject tag not found!');
		}

		$subject = Subject::firstOrCreate(['name' => $match[1]]);

		$this->subject = $subject;
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
			'name'       => $match[1],
			'subject_id' => $this->subject->id,
		]);

		$this->snippet = Snippet::create(['type' => 'slideshow']);
		$lesson->screens()->create(['snippet_id' => $this->snippet->id]);

		return true;
	}

	/**
	 * @param $data
	 * @param $slide Slide
	 * @return bool
	 */
	public function matchSection($data, $slide)
	{
		$match = [];
		$matchingResult = preg_match(self::SECTION_PATTERN, $data, $match);

		if (!$matchingResult) {
			return false;
		}

		$this->lesson->sections()->create(['name' => $match[1], 'slide_id' => $slide->id]);

		return true;
	}
}