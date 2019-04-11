<?php

use Illuminate\Database\Seeder;


class SlideshowSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run()
	{
		\Illuminate\Support\Facades\Storage::disk('s3')->getAdapter()->setBucket('wnl-platform-storage');
		$storage = \Illuminate\Support\Facades\Storage::disk('s3');
		$htmlContents = $storage->get('slideshows_test/demo/Okursie.html');

		(new \Lib\SlideParser\Parser)->parse($htmlContents);
	}
}
