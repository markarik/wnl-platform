<?php namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
	protected $fillable = ['type'];

	public function scopeType($query, $type)
	{
		return $query
			->where('type', $type)
			->first();
	}

	public function scopeCount($query, $reactable)
	{
//		DB::listen(function ($query) {
//			print $query->sql . $query->time . PHP_EOL;
//		});
		return DB::table('reactables')
			->join('reactions', 'reactions.id', '=', 'reactables.reaction_id')
			->select(DB::raw('reactions.type, count(*) count '))
			->groupBy('reactions.type')
			->where('reactable_id', $reactable->id)
			->where('reactable_type', 'App\Models\\' . class_basename($reactable))
			->get()
			->keyBy('type')
			->map(function ($el) {
				return $el->count;
			})->toArray();
	}
}
