<?php namespace App\Http\Controllers\Api\Filters\Task;

use App\Http\Controllers\Api\Filters\ApiFilter;
use App\Models\Category;

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

    public function count($builder) {
        $rootCategories = Category::where('parent_id', null)->get(['id', 'name']);

        foreach($rootCategories as $rootCategory) {
            $rootCategory['categories'] = Category::where('parent_id', $rootCategory->id)->get(['id', 'name']);
        }

        // Be smarter and think how to not hardcode it
        $rootCategories[] = [
            'id' => -1,
            'name' => 'Pomoc',
            'categories' => [
                ['name' => 'Pomoc techniczna'],
                ['name' => 'Błędy'],
                ['name' => 'Pomoc w nauce'],
                ['name' => 'Nowe funkcje'],
                ['name' => 'Sugestie'],
            ]
        ];

        return $rootCategories->toArray();
    }
}
