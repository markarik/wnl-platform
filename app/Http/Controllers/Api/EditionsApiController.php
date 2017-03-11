<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Transformers\EditionTransformer;
use App\Models\Edition;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Serializer\DataArraySerializer;

class EditionsApiController extends ApiController
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

	public function putUserProgress($editionId, $userId)
	{
		$data = [];
		return response('Created', 201)->json($data);
//		return response('OK', 200)->json($data);
	}

	public function getStructure($editionId)
	{
		$this->fractal->parseIncludes('groups.lessons');
		$edition = Edition::find($editionId);
		$resource = new Item($edition, new EditionTransformer, 'edition');

		$data = $this->fractal->createData($resource)->toArray();

//		return response(($data))->header('Content-Type', 'html; charset=utf-8');
		return response()->json($data);
	}

	protected function edition($id)
	{


	}

}
