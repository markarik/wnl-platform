<?php namespace Lib\SlideParser;

use App\Exceptions\ParseErrorException;
use App\Helpers\Url;
use App\Models\Group;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\Slide;
use App\Models\Slideshow;
use App\Models\Subsection;
use App\Models\Tag;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Facades\Lib\Bethink\Bethink;
use Storage;

class Parser
{
	const SLIDE_PATTERN = '/<section([\s\S]*?)>([\s\S]*?)<\/section>/';

	const FUNCTIONAL_SLIDE_PATTERN = '/[#!]+\(functional\)/';

	const TAG_PATTERN = '/#!\(([\w]*):([^\)]*)\)/';

	const BACKGROUND_PATTERN = '/data-background-image="([^"]*)"/';

	const LUCID_EMBED_PATTERN = '/<div[^\<]*<iframe.*lucidchart.com\/documents\/embeddedchart\/([^"]*).*<\/iframe>[^\<]*<\/div>/';

	const HEADER_PATTERN = '/<h[1-6]>([\s\S]*)<\/h[1-6]>/';

	const SUBHEADER_PATTERN = '/<[ph\d]+.*>([\s\S](?!<p.*>))*<\/[ph\d]+>/';

	const PAGE_PATTERN = '/<p>((?!<p>)[\s\S])*(\d\/\d)[\s\S]*<\/p>/';

	const IMAGE_PATTERN = '/<img.*src="(.*?)".*>/';

	const MEDIA_PATTERNS = [
		'chart' => '/<img.*class="chart".*>/',
		'movie' => '/<iframe.*youtube\.com.*>/',
		'audio' => '/<iframe.*clyp.it.*>/',
	];

	const IMAGE_TEMPLATE = '<img src="%s">';

	const IMAGE_VIEWER_TEMPLATE = '
		<div class="iv-image-container">
			<img src="%s" class="chart">
			<a class="iv-image-fullscreen" title="Pełen ekran">
				<span class="fullscreen-icon">
					<span class="inner"></span>
					<span class="horizontal"></span>
					<span class="vertical"></span>
				</span>
			</a>
		</div>';

	const GIF_TEMPLATE = '<img src="%s" class="gif">';

	protected $categoryTags;
	protected $courseTags;
	protected $questionTag = 'question';
	protected $categoryModels = [];
	protected $courseModels = [];
	protected $lessonTag;
	protected $groupTag;

	/**
	 * Parser constructor.
	 */
	public function __construct()
	{
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
			3 => 'subsection',
		]);
	}

	/**
	 * @param $fileContents - string/html
	 *
	 * @param null $screenId
	 * @param null $discussionId
	 * @param bool $enableSlidesMatching
	 */
	public function parse($fileContents, $screenId = null, $discussionId = null, $enableSlidesMatching = false)
	{
		// TODO: Unspaghettize this code
		$iteration = 0;
		$orderNumber = 0;
		$slides = $this->matchSlides($fileContents);
		Log::debug('Parsing...');
		$names = [];
		$slideshowTag = Tag::firstOrCreate(['name' => 'Prezentacja']);
		$lastSectionFound = null;

		foreach ($slides as $currentSlide => $slideHtml) {
			$iteration++;

			foreach ($this->categoryModels as $index => $model) {
				if (!in_array($model->name, $names)) {
					Log::debug("($iteration)" . str_repeat(' ', 5 - strlen($iteration)) . str_repeat('-', $index) . $model->name);
					$names[$model->name] = $model->name;
				}
			}

			$content = $this->cleanSlide($slideHtml);

			if ($enableSlidesMatching) {
				$slide = Slide::firstOrCreate(
					['content' => $content],
					[
						'content'       => $this->cleanSlide($slideHtml),
						'is_functional' => $this->isFunctional($slideHtml),
					]);
			} else {
				$slide = Slide::create([
					'content'       => $this->cleanSlide($slideHtml),
					'is_functional' => $this->isFunctional($slideHtml),
				]);
			}

			$tags = $this->getTags($slideHtml);

			$foundCourseTags = [];
			$foundQuestionsIds = [];

			foreach ($tags as $tagName => $tagValues) {
				foreach ($tagValues as $tagValue) {
					$searchResult = $this->courseTags->search($tagName);
					if ($searchResult !== false) {
						$foundCourseTags[$searchResult] = ['name' => $tagName, 'value' => $tagValue];
					}

					if ($tagName === $this->questionTag) {
						$foundQuestionsIds[] = $tagValue;
					}
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
					$this->groupTag = Tag::firstOrCreate(['name' => $group->name]);
				}

				if ($courseTag['name'] == 'lesson') {
					$lesson = Lesson::firstOrCreate([
						'name'     => $courseTag['value'],
						'group_id' => $this->courseModels['group']->id,
					]);
					$this->lessonTag = Tag::firstOrCreate(['name' => $lesson->name]);

					$slideshow = Slideshow::create([
						'background' => $this->getBackground($fileContents),
					]);
					$orderNumber = 0;
					$this->courseModels['slideshow'] = $slideshow;

					$screenData = [
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
					];

					if ($screenId) {
						$screenData['id'] = intval($screenId);
					}

					if ($discussionId) {
						$screenData['discussion_id'] = $discussionId;
						$screenData['is_discussable'] = true;
					}

					$this->courseModels['screen'] = $lesson->screens()->create($screenData);
					$this->courseModels['screen']->tags()->attach($slideshowTag);
					$this->courseModels['screen']->tags()->attach($this->lessonTag);
					$this->courseModels['screen']->tags()->attach($this->groupTag);
				}

				if ($courseTag['name'] == 'section') {
					$section = Section::firstOrCreate([
						'name'      => $this->cleanName($courseTag['value']),
						'screen_id' => $this->courseModels['screen']->id,
					]);
					$this->courseModels['section'] = $section;

					$lastSectionFound = $this->courseModels['section'];
					unset($this->courseModels['subsection']);
				}

				if ($courseTag['name'] == 'subsection') {
					$subsection = Subsection::firstOrCreate([
						'name'       => $this->cleanName($courseTag['value']),
						'section_id' => $this->courseModels['section']->id,
					]);
					$this->courseModels['subsection'] = $subsection;
				}
			}

			if ($this->lessonTag && !$slide->tags->contains($this->lessonTag)) {
				$slide->tags()->attach($this->lessonTag);
			}
			if ($this->groupTag && !$slide->tags->contains($this->groupTag)) {
				$slide->tags()->attach($this->groupTag);
			}
			$lastSectionFound = $this->attachToPresentables($slide, $orderNumber, $lastSectionFound);

			if (!empty($foundQuestionsIds)) {
				$slide->quizQuestions()->attach($foundQuestionsIds);
			}

			$orderNumber++;
		}

		\Artisan::queue('screens:countSlides');
	}

	/**
	 * @param $data - string/html
	 *
	 * @return array
	 */
	public function matchSlides($data):array
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
			$tagName = $match[1];

			if (!array_key_exists($tagName, $tags)) {
				$tags[$tagName] = [];
			}

			$tags[$tagName][] = $match[2];
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

		Storage::put($path, $image->__toString(), 'public');

		return $fileName;
	}

	public function cleanSlide($html)
	{
		$regexSearch = [
			self::TAG_PATTERN,
			self::FUNCTIONAL_SLIDE_PATTERN,
			self::BACKGROUND_PATTERN,
		];

		$textSearch = [
			'border-style: solid; border-width: 4px;',
		];

		$html = preg_replace($regexSearch, '', $html);
		$html = str_replace($textSearch, '', $html);
		$html = $this->handleCharts($html);
		$html = $this->handleImages($html);

		return $html;
	}

	public function cleanName($name)
	{
		return strip_tags($name);
	}

	public function handleCharts($html)
	{
		$match = $this->match(self::LUCID_EMBED_PATTERN, $html);

		if ($match === false) {
			return $html;
		}

		$iframe = $match[0][0];
		$chartId = $match[0][1];

		$html = preg_replace(self::LUCID_EMBED_PATTERN, $this->chartViewer($chartId), $html);

		return $html;
	}

	public function chartViewer($chartId)
	{
		$lucidUrl = 'https://www.lucidchart.com/documents/thumb/%s/0/0/NULL/%d';
		$imageSizePx = 2000;
		$urlFormatted = sprintf($lucidUrl, $chartId, $imageSizePx);
		$image = Image::make($urlFormatted)->stream('png');
		$path = "charts/{$chartId}.png";
		Storage::put('public/' . $path, $image->__toString(), 'public');

		return sprintf(
			self::IMAGE_VIEWER_TEMPLATE,
			Bethink::getAssetPublicUrl($path)
		);
	}

	public function createSnippet($slideHtml)
	{
		$snippet = [
			'header'    => '',
			'subheader' => '',
			'content'   => '',
			'media'     => null,
			'page'      => '',
		];

		$match = $this->match(self::HEADER_PATTERN, $slideHtml);

		if ($match) {
			$header = $match[0][1];
			$header = str_replace('<br>', ' ', $header);
			$header = strip_tags($header);
			$header = trim($header);
			$snippet['header'] = $header;
			$slideHtml = preg_replace(self::HEADER_PATTERN, '', $slideHtml);
		}

		$match = $this->match(self::SUBHEADER_PATTERN, $slideHtml);

		if ($match) {
			$subheader = strip_tags($match[0][0]);
			if ($this->match('/\w+/', $subheader)) {
				$snippet['subheader'] = trim($subheader);
			}
		}

		foreach (self::MEDIA_PATTERNS as $name => $pattern) {
			$match = $this->match($pattern, $slideHtml);
			if ($match) {
				$snippet['media'] = $name;
			}
		}

		$match = $this->match(self::PAGE_PATTERN, $slideHtml);

		if ($match) {
			$snippet['page'] = $match[0][2];
			$slideHtml = preg_replace(self::PAGE_PATTERN, '', $slideHtml);
		}

		$slideHtml = str_replace(["\n", "\r"], '|', $slideHtml);
		$slideHtml = str_replace('&nbsp;', '', $slideHtml);
		$slideHtml = preg_replace('/p>\s*\-/', 'p>br-', $slideHtml);
		$slideHtml = strip_tags($slideHtml);
		$slideHtml = str_replace('br-', '<br>-', $slideHtml);

		if (!$this->match('/\w/', $slideHtml)) {
			$snippet['content'] = '';

			return $snippet;
		}

		$slideHtml = preg_replace('/[\|\s]+/', ' ', $slideHtml);
		$slideHtml = preg_replace('/\s+/', ' ', $slideHtml);
		$slideHtml = preg_replace('/^\s/', '', $slideHtml);
		$slideHtml = preg_replace('/\(\s+/', '(', $slideHtml);
		$slideHtml = preg_replace('/\s+\)/', ')', $slideHtml);

		$snippet['content'] = trim($slideHtml);

		return $snippet;
	}

	public function handleImages($html)
	{
		$matches = $this->match(self::IMAGE_PATTERN, $html);

		if ($matches === false) {
			return $html;
		}

		foreach ($matches as $match) {
			$imgTag = $match[0];
			$imageUrl = $match[1];

			if (stripos($imgTag, 'data-') === false) {
				// Check if img tag contains data attributes - if not we don't need to migrate it
				continue;
			}

			try {
				$image = Image::make(Url::encodeFullUrl($imageUrl));
			}
			catch (\Exception $e) {
				\Log::error("Fetching image from {$imageUrl} failed with message: {$e->getMessage()}.");
				continue;
			}

			$mime = $image->mime;
			$supported = ['image/jpeg', 'image/gif', 'image/png'];
			if (!in_array($mime, $supported)) {
				\Log::error("Unsupported image type: {$mime}");

				continue;
			}

			$template = self::IMAGE_TEMPLATE;

			if ($mime === 'image/gif') {
				$data = @file_get_contents($imageUrl);
				$ext = 'gif';
				$template = self::GIF_TEMPLATE;
			} else if ($mime === 'image/png') {
				$data = $image->resize(1920, 1080, function ($constraint) {
					$constraint->aspectRatio();
					$constraint->upsize();
				})->stream('png');
				$ext = 'png';
			}
			else {
				$background = $image->resize(1920, 1080, function ($constraint) {
					$constraint->aspectRatio();
					$constraint->upsize();
				});
				$canvas = Image::canvas($image->width(), $image->height(), '#fff');
				$data = $canvas->insert($background)->stream('jpg', 80);
				$ext = 'jpg';
			}

			if (!$data) {
				\Log::error("Fetching image from {$imageUrl} failed.");

				continue;
			}

			$path = 'uploads/' . date('Y/m') . '/' . str_random(32) . '.' . $ext;
			Storage::put('public/' . $path, $data->__toString(), 'public');

			$viewerHtml = sprintf($template, Bethink::getAssetPublicUrl($path));
			$html = str_replace($imgTag, $viewerHtml, $html);
		}

		return $html;
	}

	/**
	 * @param $slide
	 * @param $orderNumber
	 * @param $lastSectionFound
	 */
	protected function attachToPresentables($slide, $orderNumber, $lastSectionFound)
	{
		try {
			if (array_key_exists('slideshow', $this->courseModels)) {

				$this->courseModels['slideshow']->slides()->attach($slide, ['order_number' => $orderNumber]);
			}
			if (array_key_exists('section', $this->courseModels)) {
				$this->courseModels['section']->slides()->attach($slide, ['order_number' => $orderNumber]);

				if ($lastSectionFound === null) {
					$lastSectionFound = $this->courseModels['section'];
				} else if ($lastSectionFound->name !== $this->courseModels['section']->name) {
					$lastSectionFound = $this->courseModels['section'];
				}
			}

			if (array_key_exists('subsection', $this->courseModels)) {
				$this->courseModels['subsection']->slides()->attach($slide, ['order_number' => $orderNumber]);
			}
		}
		catch (\Illuminate\Database\QueryException $e) {
			if ($e->errorInfo[1] === 1062) {
				// Means slide is duplicated.
			} else {
				throw $e;
			}
		} finally {
			return $lastSectionFound;
		}
	}
}
