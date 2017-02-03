<?php

namespace Lib\SlideParser;

use App\Exceptions\ParseErrorException;
use App\Models\Slide;
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
				'content'       => $content,
				'is_functional' => $this->isFunctional($content),
			]);

			if ($key === 0){
				$this->subject = $this->matchSubject($slide);
			}

			$this->subject->slides()->attach($slide);

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
	 * @return Subject
	 * @throws ParseErrorException
	 */
	public function matchSubject($data)
	{
		$match = [];
		$matchingResult = preg_match(self::SUBJECT_PATTERN, $data, $match);

		if (!$matchingResult){
			throw new ParseErrorException('Subject tag not found!');
		}

		$subject = Subject::firstOrCreate(['name' => $match[1]]);

		return $subject;
	}
}