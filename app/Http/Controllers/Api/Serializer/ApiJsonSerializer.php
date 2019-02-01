<?php

namespace App\Http\Controllers\Api\Serializer;

use Illuminate\Support\Str;
use League\Fractal\Pagination\CursorInterface;
use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Serializer\SerializerAbstract;

class ApiJsonSerializer extends SerializerAbstract
{
	protected $relationships;
	protected $includes;
	protected $currentlyProcessedResource;

	public function __construct()
	{
		$this->includes = [];
		$this->relationships = [];
	}

	/**
	 * Serialize a collection.
	 *
	 * @param string $resourceKey
	 * @param array $data
	 *
	 * @return array
	 */
	public function collection($resourceKey, array $data)
	{
//		var_dump(__METHOD__, $resourceKey);

		return $data;
	}

	/**
	 * Serialize an item.
	 *
	 * @param string $resourceKey
	 * @param array $data
	 *
	 * @return array
	 */
	public function item($resourceKey, array $data)
	{
//		var_dump(__METHOD__, $resourceKey);

		return $data;
	}

	/**
	 * Serialize null resource.
	 *
	 * @return array
	 */
	public function null()
	{
		return [];
	}

	/**
	 * Serialize the included data.
	 *
	 * @param ResourceInterface $resource
	 * @param array $data
	 *
	 * @return array
	 */
	public function includedData(ResourceInterface $resource, array $data)
	{
		$resourceKey = $resource->getResourceKey();

		$this->currentlyProcessedResource = $resourceKey;

		foreach ($data as $includedResources) {
			if (empty ($includedResources)) continue;

			foreach ($includedResources as $includedResourceName => $items) {
				if (array_key_exists('id', $items)) $items = [$items];
				foreach ($items as $item) {
					if (!array_key_exists($resourceKey, $item)) continue;
					$resourceId = $item[$resourceKey];
					$pluralIncludedResourceName = Str::plural($includedResourceName);

					if (!isset($this->relationships[$resourceKey][$resourceId][$pluralIncludedResourceName]) ||
						!in_array($item['id'], $this->relationships[$resourceKey][$resourceId][$pluralIncludedResourceName], true)) {
						// Don't duplicate relation id when parent resource was included multiple times
						$this->relationships[$resourceKey][$resourceId][$pluralIncludedResourceName][] = $item['id'];
					}
					$this->includes[$pluralIncludedResourceName][$item['id']] = $item;
				}
			}
		}

		return $data;
	}

	/**
	 * @param array $data
	 * @param array $includedData
	 *
	 * @return array
	 */
	public function injectData($data, $includedData)
	{
//		var_dump(__METHOD__, $data);
//		echo '<pre>';
//		print_r($this->relationships);
//		echo '</pre>';

		if (!array_key_exists($this->currentlyProcessedResource, $this->relationships)) {
			return $data;
		}

		if (array_key_exists('id', $data)) {
			$relationships = $this->relationships[$this->currentlyProcessedResource][$data['id']];
			$data = array_merge($data, $relationships);
		} else {
			$data = array_map(function ($item) {
				$relationships = [];
				if (array_key_exists($this->currentlyProcessedResource, $this->relationships)) {
					if (array_key_exists($item['id'], $this->relationships[$this->currentlyProcessedResource])) {
						$relationships = $this->relationships[$this->currentlyProcessedResource][$item['id']];
					}

					return array_merge($item, $relationships);
				} else {
					return $item;
				}

			}, $data);
		}

		return $data;
	}

	/**
	 * Hook to manipulate the final sideloaded includes.
	 *
	 * @param array $includedData
	 * @param array $data
	 *
	 * @return array
	 */
	public function filterIncludes($includedData, $data)
	{

		return empty($this->includes) ? [] : ['included' => $this->includes];
	}

	/**
	 * Serialize the meta.
	 *
	 * @param array $meta
	 *
	 * @return array
	 */
	public function meta(array $meta)
	{
		if (empty($meta)) {
			return [];
		}

		return ['meta' => $meta];
	}

	/**
	 * Serialize the paginator.
	 *
	 * @param PaginatorInterface $paginator
	 *
	 * @return array
	 */
	public function paginator(PaginatorInterface $paginator)
	{
		$currentPage = (int)$paginator->getCurrentPage();
		$lastPage = (int)$paginator->getLastPage();

		$pagination = [
			'total'        => (int)$paginator->getTotal(),
			'count'        => (int)$paginator->getCount(),
			'per_page'     => (int)$paginator->getPerPage(),
			'current_page' => $currentPage,
			'total_pages'  => $lastPage,
		];

		$pagination['links'] = [];

		if ($currentPage > 1) {
			$pagination['links']['previous'] = $paginator->getUrl($currentPage - 1);
		}

		if ($currentPage < $lastPage) {
			$pagination['links']['next'] = $paginator->getUrl($currentPage + 1);
		}

		return ['pagination' => $pagination];
	}

	/**
	 * Serialize the cursor.
	 *
	 * @param CursorInterface $cursor
	 *
	 * @return array
	 */
	public function cursor(CursorInterface $cursor)
	{
		$cursor = [
			'current' => $cursor->getCurrent(),
			'prev'    => $cursor->getPrev(),
			'next'    => $cursor->getNext(),
			'count'   => (int)$cursor->getCount(),
		];

		return ['cursor' => $cursor];
	}

	/**
	 * Indicates if includes should be side-loaded.
	 *
	 * @return bool
	 */
	public function sideloadIncludes()
	{
		return true;
	}
}
