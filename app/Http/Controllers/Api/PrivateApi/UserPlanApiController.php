<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Models\User;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\QuizQuestion;
use App\Models\UserPlan;
use Carbon\Carbon;

class UserPlanApiController extends ApiController
{

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.user-plan');
	}

	public function get($userId)
	{
		return [
			"questions" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
		];
	}

	public function post(Request $request) {
		$startDate = $request->get('startDate');
		$endDate = $request->get('endDate');
		$slackDays = $request->get('slackDays');
		$userId = $request->route('userId');

		// TODO handle empty dates

		$filteredQuestions = $this->addFilters($request->filters, app('App\\Models\\QuizQuestion'))->get()->pluck('id');
		$activeDays = (new \DateTime($endDate))->diff(new \DateTime($startDate))->format('%a') - $slackDays;

		$createdPlan = UserPlan::create([
			'user_id' => $userId,
			'start_date' => Carbon::parse($startDate),
			'end_date' => Carbon::parse($endDate),
			'slack_days_planned' => $slackDays,
			'slack_days_left' => $slackDays
		]);

		$valuesToInsert = [];

		foreach($filteredQuestions as $question) {
			$valuesToInsert[] = [
				'user_id' => $userId,
				'plan_id' => $createdPlan->id,
				'question_id' => $question
			];
		}

		\DB::table('users_plan_progress')->insert($valuesToInsert);

		$this->respondOk();
	}
}
