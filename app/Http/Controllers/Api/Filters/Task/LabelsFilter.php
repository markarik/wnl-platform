<?php namespace App\Http\Controllers\Api\Filters\Task;

use App\Http\Controllers\Api\Filters\ApiFilter;
use App\Models\Task;
use App\Http\Controllers\Api\Concerns\TranslatesApiQueries;
use Illuminate\Http\Request;

class LabelsFilter extends ApiFilter
{
	public function handle($model)
    {
        return $model->where(function ($query) {
            return $query->whereRaw('JSON_CONTAINS(labels, \'{"tags":["'. $this->params .'"]}\')');
        });
    }
}
