<?php namespace App\Models;

use DB;
use Auth;
use App\Events\ReactionAdded;
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
			->select(DB::raw('reactions.type, count(*) count'))
			->join('reactions', 'reactions.id', '=', 'reactables.reaction_id')
			->groupBy('reactions.type')
			->where('reactable_id', $reactable->id)
			->where('reactable_type', 'App\Models\\' . class_basename($reactable))
			->get();
	}

	public function scopeFlags($query, $reactable)
	{
		$userId = Auth::user()->id;

		return DB::table('reactables')
			->select(DB::raw('reactions.type, count(*) count'))
			->join('reactions', 'reactions.id', '=', 'reactables.reaction_id')
			->groupBy('reactions.type')
			->where('reactable_id', $reactable->id)
			->where('reactable_type', 'App\Models\\' . class_basename($reactable))
			->where('reactables.user_id', $userId)
			->get();
	}
}
