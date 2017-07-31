<?php

namespace App\Traits;

use App\Models\Presentable;
use Illuminate\Database\Eloquent\Model;

trait EventContextTrait {
	protected function addEventContext(Model $model)
	{
		switch (get_class($model)) {
			case 'App\Models\QnaQuestion':
				if (!empty($model->meta)) {
					return $model->meta['context'];
				}
				return [];

			case 'App\Models\QnaAnswer':
				if (!empty($model->question->meta)) {
					return $model->question->meta['context'];
				}
				return [];

			case 'App\Models\Slide':
				if (!empty($model->sections)) {
					$section = $model->sections->first();
					$screen = $section->screen;
					$lesson = $screen->lesson;
					$orderNumber = (int) Presentable::where([
						['presentable_type', '=', 'App\\Models\\Section'],
						['slide_id', '=', $model->id],
					])->get()->first()->order_number;

					return [
						'name' => 'screens',
						'params' => [
							'courseId' => $lesson->group->course->id,
							'lessonId' => $lesson->id,
							'screenId' => $screen->id,
							'slide'  => $orderNumber + 1,
						],
					];
				}
				return [];

			default:
				return [];
		}
	}
}
