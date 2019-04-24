<?php

namespace App\Traits;

use App\Models\Comment;
use App\Models\Presentable;
use App\Models\QnaAnswer;
use App\Models\QnaQuestion;
use App\Models\QuizQuestion;
use App\Models\Slide;
use Facades\App\Contracts\CourseProvider;
use Illuminate\Database\Eloquent\Model;

trait EventContextTrait {
	protected function addEventContext(Model $model)
	{
		switch (get_class($model)) {
			case 'App\Models\QnaQuestion':
				/** @var QnaQuestion $model */
				if (!empty($model->meta)) {
					return [
						'dynamic' => [
							'resource' => 'qna_questions',
							'value' => $model->id,
						],
						'route' => $model->meta['context'],
						// params are used to determine if user already started a lesson from QnaQuestion context
						'params' => $model->meta['context']['params'] ?? []
					];
				}
				return [];

			case 'App\Models\QnaAnswer':
				/** @var QnaAnswer $model */
				if (!empty($model->question)) {
					return $this->addEventContext($model->question);
				}
				return [];

			case 'App\Models\QuizQuestion':
				/** @var QuizQuestion $model */
				if (!empty($model)) {
					return [
						'name' => 'quizQuestion',
						'params' => [
							'id' => $model->id,
						],
					];
				}
				return [];

			case 'App\Models\Slide':
				/** @var Slide $model */
				if ($model->sections->count() > 0) {
					$section = $model->sections->first();
					$screen = $section->screen;
					$lesson = $screen->lesson;
					$orderNumber = (int) Presentable::where([
						['presentable_type', '=', 'App\\Models\\Section'],
						['slide_id', '=', $model->id],
					])->first()->order_number;

					return [
						'name' => 'lessons',
						'params' => [
							'courseId' => CourseProvider::getCourseId(),
							'lessonId' => $lesson->id,
							'screenId' => $screen->id,
							'slide'  => $orderNumber + 1
						],
					];
				}
				return [];

			case 'App\Models\Comment':
				/** @var Comment $model */
				return $this->addEventContext($model->commentable);

			default:
				return [];
		}
	}
}
