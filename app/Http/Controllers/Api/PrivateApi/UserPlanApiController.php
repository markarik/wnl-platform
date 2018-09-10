<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\UserPlanTransformer;
use App\Models\UserPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;

class UserPlanApiController extends ApiController
{

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.user-plan');
	}

	public function get($userId)
	{
		$plan = UserPlan::where('user_id', $userId)->get()->last();

		if (!$plan) return $this->respondNoContent();

		$resource = new Item($plan, new UserPlanTransformer, $this->resourceName);
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function post(Request $request)
	{
		$startDate = $request->get('startDate');
		$endDate = $request->get('endDate');
		$slackDays = $request->get('slackDays');
		$userId = $request->route('userId');
		$filters = $request->get('filters');
		$preserveProgress = $request->get('preserveProgress');
		$recentPlan = UserPlan::where('user_id', $userId)->get()->last();

		// TODO handle empty dates
		$filteredQuestions = $this
			->addFilters($request->filters, app('App\\Models\\QuizQuestion'))
			->get()
			->pluck('id');

		$createdPlan = UserPlan::create([
			'user_id'            => $userId,
			'start_date'         => Carbon::parse($startDate),
			'end_date'           => Carbon::parse($endDate),
			'slack_days_planned' => $slackDays,
			'slack_days_left'    => $slackDays,
			'filters'            => $filters,
		]);

		$valuesToInsert = collect();

		foreach ($filteredQuestions as $question) {
			$valuesToInsert->push([
				'user_id'     => $userId,
				'plan_id'     => $createdPlan->id,
				'question_id' => $question,
			]);
		}

		if ($recentPlan && $preserveProgress === true) {
			$valuesToInsert = $this->rewriteProgress($recentPlan, $valuesToInsert);
		}

		\DB::table('users_plan_progress')->insert($valuesToInsert->toArray());

		$this->deleteProgress($recentPlan);

		$resource = new Item($createdPlan, new UserPlanTransformer, $this->resourceName);
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	protected function rewriteProgress($recentPlan, $valuesToInsert)
	{
		$recentProgress = $recentPlan->questionsProgress->keyBy('question_id');
		$valuesToInsert = $valuesToInsert->map(function ($item) use ($recentProgress) {
			$item['resolved_at'] = $recentProgress->get($item['question_id'])->resolved_at ?? null;

			return $item;
		});

		return $valuesToInsert;
	}

	protected function deleteProgress($plan)
	{
		if (!$plan) return;
		$plan->questionsProgress()->delete();
	}
}
