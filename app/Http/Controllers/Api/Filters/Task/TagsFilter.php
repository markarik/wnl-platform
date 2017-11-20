<?php namespace App\Http\Controllers\Api\Filters\Task;

use App\Http\Controllers\Api\Filters\ApiFilter;
use App\Models\Task;
use App\Http\Controllers\Api\Filters\ByTaxonomy;
use App\Http\Controllers\Api\Concerns\TranslatesApiQueries;

class TagsFilter extends ByTaxonomyFilter
{
	public function handle($model)
    {
        return $model->where('subject_type', function ($query) {
            $query->whereHas('tags', function($secondQuery) {
                $secondQuery->whereIn('tags.id', $this->params);
            });
        });
    }

    public function count($builder)
	{
		return parent::taxonomyCounters($builder, 'subjects');
	}
}
