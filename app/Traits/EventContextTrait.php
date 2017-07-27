<?php

namespace App\Traits;

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

			default:
				return [];
		}
	}
}