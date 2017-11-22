<?php namespace App\Http\Controllers\Api\Filters\Task;

use App\Http\Controllers\Api\Filters\ApiFilter;

class LabelsFilter extends ApiFilter
{
    protected $expected = ['list'];

    public function handle($model)
    {
        return $model->where(function ($query) {
            foreach($this->params['list'] as $label) {
                $query->orWhereRaw('JSON_CONTAINS(labels, \'{"tags":["'. $label .'"]}\')');
            }
        });
    }
}
