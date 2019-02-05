<?php
/**
 * Created by PhpStorm.
 * User: kvas
 * Date: 05.02.19
 * Time: 13:49
 */

namespace Tests\Unit\Http\Controllers\Api\Serializer;

use App\Http\Controllers\Api\Serializer\ApiJsonSerializer;
use League\Fractal\Resource\Item;
use Tests\TestCase;

class ApiJsonSerializerTest extends TestCase
{
	public function testIncludeCollection() {
		$apiJsonSerializer = new ApiJsonSerializer();
		$resource = new Item([], function() {}, 'taxonomy_terms');

		$apiJsonSerializer->includedData($resource,
			[
				[
					'tags' => [
						[
							'id' => 1,
							'name' => 'test',
							'taxonomy_terms' => 2
						],
						[
							'id' => 2,
							'name' => 'test2',
							'taxonomy_terms' => 2
						],
					]
				]
			]
		);

		$includes = $apiJsonSerializer->filterIncludes([], []);
		$incjectedData = $apiJsonSerializer->injectData([
			'id'=> 2,
			'name' => 'test'
		], []);

		$this->assertArrayHasKey('included', $includes);
		$this->assertArrayHasKey('tags', $includes['included']);
		$this->assertCount(2, $includes['included']['tags']);
		$this->assertArrayHasKey('tags', $incjectedData);
		$this->assertCount(2, $incjectedData['tags']);
	}

	public function testIncludeCollectionWithDuplicates() {
		$apiJsonSerializer = new ApiJsonSerializer();
		$resource = new Item([], function() {}, 'taxonomy_terms');

		$apiJsonSerializer->includedData($resource,
			[
				[
					'tags' => [
						[
							'id' => 1,
							'name' => 'test',
							'taxonomy_terms' => 2
						],
						[
							'id' => 2,
							'name' => 'test2',
							'taxonomy_terms' => 2
						],
					]
				]
			]
		);
		$apiJsonSerializer->includedData($resource,
			[
				[
					'tags' => [
						[
							'id' => 3,
							'name' => 'test',
							'taxonomy_terms' => 2
						],
						[
							'id' => 2,
							'name' => 'test2',
							'taxonomy_terms' => 2
						],
					]
				]
			]
		);

		$includes = $apiJsonSerializer->filterIncludes([], []);
		$incjectedData = $apiJsonSerializer->injectData([
			'id'=> 2,
			'name' => 'test'
		], []);

		$this->assertArrayHasKey('included', $includes);
		$this->assertArrayHasKey('tags', $includes['included']);
		$this->assertCount(3, $includes['included']['tags']);
		$this->assertArrayHasKey('tags', $incjectedData);
		$this->assertCount(3, $incjectedData['tags']);
	}

	public function testIncludeItem() {
		$apiJsonSerializer = new ApiJsonSerializer();
		$resource = new Item([], function() {}, 'taxonomy_terms');

		$apiJsonSerializer->includedData($resource,
			[
				[
					'tag' => [
						'id' => 1,
						'name' => 'test',
						'taxonomy_terms' => 2
					],
				]
			]
		);

		$includes = $apiJsonSerializer->filterIncludes([], []);
		$incjectedData = $apiJsonSerializer->injectData([
			'id'=> 2,
			'name' => 'test'
		], []);

		$this->assertArrayHasKey('included', $includes);
		$this->assertArrayHasKey('tags', $includes['included']);
		$this->assertCount(1, $includes['included']['tags']);
		$this->assertArrayHasKey('tag', $incjectedData);
		// TODO PLAT-971 convert migrate array to int
		$this->assertCount(1, $incjectedData['tag']);
	}

	public function testIncludeItemWithDuplicate() {
		$apiJsonSerializer = new ApiJsonSerializer();
		$resource = new Item([], function() {}, 'taxonomy_terms');

		$apiJsonSerializer->includedData($resource,
			[
				[
					'tag' => [
						'id' => 1,
						'name' => 'test',
						'taxonomy_terms' => 3
					],
				]
			]
		);
		$apiJsonSerializer->includedData($resource,
			[
				[
					'tag' => [
						'id' => 1,
						'name' => 'test',
						'taxonomy_terms' => 1
					],
				]
			]
		);
		$apiJsonSerializer->includedData($resource,
			[
				[
					'tag' => [
						'id' => 3,
						'name' => 'test2',
						'taxonomy_terms' => 2
					],
				]
			]
		);

		$includes = $apiJsonSerializer->filterIncludes([], []);
		$incjectedData = $apiJsonSerializer->injectData([
			[
				'id'=> 1,
				'name' => 'test'
			],
			[
				'id'=> 2,
				'name' => 'test'
			],
			[
				'id'=> 3,
				'name' => 'test'
			],
		], []);

		$this->assertArrayHasKey('included', $includes);
		$this->assertArrayHasKey('tags', $includes['included']);
		$this->assertCount(2, $includes['included']['tags']);

		$this->assertCount(3, $incjectedData);
		// TODO PLAT-971 migrate array to int
		$this->assertCount(1, $incjectedData[0]['tag']);
		$this->assertContains(1, $incjectedData[0]['tag']);
		$this->assertCount(1, $incjectedData[1]['tag']);
		$this->assertContains(3, $incjectedData[1]['tag']);
		$this->assertCount(1, $incjectedData[2]['tag']);
		$this->assertContains(1, $incjectedData[2]['tag']);
	}
}
