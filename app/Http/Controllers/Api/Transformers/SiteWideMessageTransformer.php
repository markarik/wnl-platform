<?php


namespace App\Http\Controllers\Api\Transformers;

use App\Http\Controllers\Api\ApiTransformer;
use App\Models\SiteWideMessage;


class SiteWideMessageTransformer extends ApiTransformer
{
	public function transform(SiteWideMessage $siteWideMessage)
	{
		$data = [
			'id'             => $siteWideMessage->id,
			'slug'           => $siteWideMessage->slug,
			'message'        => $siteWideMessage->message,
			'start_date'     => $siteWideMessage->start_date->timestamp,
			'end_date'       => $siteWideMessage->end_date->timestamp,
		];

		return $data;
	}
}
