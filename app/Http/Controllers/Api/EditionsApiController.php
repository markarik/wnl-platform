<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EditionsApiController extends Controller
{
	/**
	 * @param $editionId
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function get($editionId)
	{
		$data = [];

		return response()->json($data);
	}

	/**
	 * @param $editionId
	 * @param $lessonId
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getWithLessonAvailability($editionId, $lessonId)
	{
		$data = [
			'gorup' => [
				'lessons' => [
					[
						'id'          => 1,
						'name'        => '123',
						'isAvailable' => true,
					],
				],
			],
		];

		return response()->json();
	}

	/**
	 * @param $editionId
	 * @param $userId
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getWithUserProgress($editionId, $userId)
	{
		$data = [
			'lessons' => [
				1 => [
					'status' => 'done',
					'route'  => [],
				],
			],
		];

		return response()->json($data);
	}
}
