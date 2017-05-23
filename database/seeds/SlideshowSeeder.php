<?php

use Illuminate\Database\Seeder;
use Lib\SlideParser\Parser;

class SlideshowSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @param Parser $parser
	 */
	public function run(Parser $parser)
	{
		$html = DatabaseSeeder::file('example_slideshow.html');
		$parser->parse($html);
	}
}
