<?php

namespace Tests\lib\SlideParser;

use Illuminate\Support\Facades\Storage;
use Lib\SlideParser\Parser;
use Mockery;
use Tests\TestCase;

class ParserTest extends TestCase
{
	/**
	 * @param string $input
	 * @param string $expectedOutput
	 * @dataProvider cleanSlideProvider
	 */
	public function testCleanSlide(string $input, string $expectedOutput)
	{
		Storage::fake();
		Mockery::mock('overload:Str')->shouldReceive('random')->andReturn('RANDOM');

		$this->assertEquals($expectedOutput, (new FakeParser)->cleanSlide($input));
	}

	public function cleanSlideProvider(): \Generator
	{
		yield [
			'
<section data-id="3ecfab0054259c9b2711ce15ae825792" data-background-image="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7">
	<div class="sl-block" data-block-type="line" style="width: auto; height: auto; left: 81px; top: 101px;" data-block-id="4c20543565ae2045be03f23b57ff1281">
		<div class="sl-block-content" data-line-x1="-299" data-line-y1="-180" data-line-x2="500" data-line-y2="-180" data-line-color="#052c50" data-line-start-type="none" data-line-end-type="none" style="z-index: 11;" data-line-width="2px" data-line-style="dotted">
			<svg xmlns="http://www.w3.org/2000/svg" version="1.1" preserveaspectratio="xMidYMid" width="799" height="1" viewbox="-299 -180 799 1">
				<line stroke="rgba(0,0,0,0)" stroke-width="15" x1="-299" y1="-180" x2="500" y2="-180"></line>
				<line stroke="#052c50" stroke-width="2" stroke-dasharray="0 3.995" stroke-linecap="round" x1="-299" y1="-180" x2="500" y2="-180"></line>
			</svg>
		</div>
	</div>


	<div class="sl-block" data-block-type="line" style="width: auto; height: auto; left: 81px; top: 648px;" data-block-id="1512ba90bce9b14ceb0edbbc8d8c4b16">
		<div class="sl-block-content" data-line-x1="-302" data-line-y1="-180" data-line-x2="497" data-line-y2="-180" data-line-color="#052c50" data-line-start-type="none" data-line-end-type="none" style="z-index: 12;" data-line-width="2px" data-line-style="dotted">
			<svg xmlns="http://www.w3.org/2000/svg" version="1.1" preserveaspectratio="xMidYMid" width="799" height="1" viewbox="-302 -180 799 1">
				<line stroke="rgba(0,0,0,0)" stroke-width="15" x1="-302" y1="-180" x2="497" y2="-180"></line>
				<line stroke="#052c50" stroke-width="2" stroke-dasharray="0 3.995" stroke-linecap="round" x1="-302" y1="-180" x2="497" y2="-180"></line>
			</svg>
		</div>
	</div>


	<div class="sl-block" data-block-type="text" data-block-id="829e20389736ea8641d5675af0d95d71" style="height: auto; min-width: 30px; min-height: 30px; width: 699px; left: 81px; top: 22px;">
		<div class="sl-block-content" data-placeholder-tag="p" data-placeholder-text="Text" style="z-index: 13; color: rgb(5, 44, 80); border-width: 1px;">
			<h1 style="text-align:left">REDACTED</h1>
		</div>
	</div>
	<div class="sl-block" data-block-type="text" style="width: 806px; left: 80px; top: 101px; height: auto;" data-block-id="6438b1ffabaa167c5e44fca1c9bf1fde">
		<div class="sl-block-content" data-placeholder-tag="h2" data-placeholder-text="Subtitle" style="z-index: 14; color: rgb(7, 55, 99); text-align: left;" dir="ui">
			<p><span style="font-size:0.5em"><strong>REDACTED</strong>REDACTED</span></p>
		</div>
	</div>
	<div class="sl-block" data-block-type="image" data-block-id="b2b0a98e96bd8c35dce58a4f2b63d178" style="min-width: 4px; min-height: 4px; width: 450px; height: 321px; left: 253px; top: 224px;">
		<div class="sl-block-content" style="z-index: 15;"><img data-natural-width="450" data-natural-height="321" style="" data-lazy-loaded="" data-src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"></div>
	</div>

	<div class="sl-block" data-block-type="text" style="width: 806px; left: 75px; top: 643px; height: auto;" data-block-id="38cbcbfe0f25ee0e0406acb3123e4b81">
		<div class="sl-block-content" data-placeholder-tag="h2" data-placeholder-text="Subtitle" style="z-index: 16; color: rgb(7, 55, 99); text-align: left;" dir="ui">
			<p style="text-align: right;"><span style="font-size:0.5em">REDACTED</span></p>
		</div>
	</div>
	<div class="sl-block" data-block-type="text" data-block-id="eb33057527c7fda0df13087a612b6036" style="height: auto; min-width: 30px; min-height: 30px; width: 600px; left: 183px; top: 155px;">
		<div class="sl-block-content" data-placeholder-tag="p" data-placeholder-text="Text" style="z-index: 17;">
			<p>#!(group:REDACTED)#!(lesson:REDACTED)</p>
		</div>
	</div>
	<div class="sl-block" data-block-type="text" data-block-id="c58c3f8050f55dd8f6049b12176098c1" style="height: auto; min-width: 30px; min-height: 30px; width: 600px; left: 178px; top: 575px;">
		<div class="sl-block-content" data-placeholder-tag="p" data-placeholder-text="Text" style="z-index: 18;">
			<p>#!(section:REDACTED)</p>
		</div>
	</div>
</section>',
			'
<section data-id="3ecfab0054259c9b2711ce15ae825792" >
	<div class="sl-block" data-block-type="line" style="width: auto; height: auto; left: 81px; top: 101px;" data-block-id="4c20543565ae2045be03f23b57ff1281">
		<div class="sl-block-content" data-line-x1="-299" data-line-y1="-180" data-line-x2="500" data-line-y2="-180" data-line-color="#052c50" data-line-start-type="none" data-line-end-type="none" style="z-index: 11;" data-line-width="2px" data-line-style="dotted">
			<svg xmlns="http://www.w3.org/2000/svg" version="1.1" preserveaspectratio="xMidYMid" width="799" height="1" viewbox="-299 -180 799 1">
				<line stroke="rgba(0,0,0,0)" stroke-width="15" x1="-299" y1="-180" x2="500" y2="-180"></line>
				<line stroke="#052c50" stroke-width="2" stroke-dasharray="0 3.995" stroke-linecap="round" x1="-299" y1="-180" x2="500" y2="-180"></line>
			</svg>
		</div>
	</div>


	<div class="sl-block" data-block-type="line" style="width: auto; height: auto; left: 81px; top: 648px;" data-block-id="1512ba90bce9b14ceb0edbbc8d8c4b16">
		<div class="sl-block-content" data-line-x1="-302" data-line-y1="-180" data-line-x2="497" data-line-y2="-180" data-line-color="#052c50" data-line-start-type="none" data-line-end-type="none" style="z-index: 12;" data-line-width="2px" data-line-style="dotted">
			<svg xmlns="http://www.w3.org/2000/svg" version="1.1" preserveaspectratio="xMidYMid" width="799" height="1" viewbox="-302 -180 799 1">
				<line stroke="rgba(0,0,0,0)" stroke-width="15" x1="-302" y1="-180" x2="497" y2="-180"></line>
				<line stroke="#052c50" stroke-width="2" stroke-dasharray="0 3.995" stroke-linecap="round" x1="-302" y1="-180" x2="497" y2="-180"></line>
			</svg>
		</div>
	</div>


	<div class="sl-block" data-block-type="text" data-block-id="829e20389736ea8641d5675af0d95d71" style="height: auto; min-width: 30px; min-height: 30px; width: 699px; left: 81px; top: 22px;">
		<div class="sl-block-content" data-placeholder-tag="p" data-placeholder-text="Text" style="z-index: 13; color: rgb(5, 44, 80); border-width: 1px;">
			<h1 style="text-align:left">REDACTED</h1>
		</div>
	</div>
	<div class="sl-block" data-block-type="text" style="width: 806px; left: 80px; top: 101px; height: auto;" data-block-id="6438b1ffabaa167c5e44fca1c9bf1fde">
		<div class="sl-block-content" data-placeholder-tag="h2" data-placeholder-text="Subtitle" style="z-index: 14; color: rgb(7, 55, 99); text-align: left;" dir="ui">
			<p><span style="font-size:0.5em"><strong>REDACTED</strong>REDACTED</span></p>
		</div>
	</div>
	<div class="sl-block" data-block-type="image" data-block-id="b2b0a98e96bd8c35dce58a4f2b63d178" style="min-width: 4px; min-height: 4px; width: 450px; height: 321px; left: 253px; top: 224px;">
		<div class="sl-block-content" style="z-index: 15;"><img class="gif" src="http://platform.local/storage/FAKE_PATH.gif"></div>
	</div>

	<div class="sl-block" data-block-type="text" style="width: 806px; left: 75px; top: 643px; height: auto;" data-block-id="38cbcbfe0f25ee0e0406acb3123e4b81">
		<div class="sl-block-content" data-placeholder-tag="h2" data-placeholder-text="Subtitle" style="z-index: 16; color: rgb(7, 55, 99); text-align: left;" dir="ui">
			<p style="text-align: right;"><span style="font-size:0.5em">REDACTED</span></p>
		</div>
	</div>
	<div class="sl-block" data-block-type="text" data-block-id="eb33057527c7fda0df13087a612b6036" style="height: auto; min-width: 30px; min-height: 30px; width: 600px; left: 183px; top: 155px;">
		<div class="sl-block-content" data-placeholder-tag="p" data-placeholder-text="Text" style="z-index: 17;">
			<p></p>
		</div>
	</div>
	<div class="sl-block" data-block-type="text" data-block-id="c58c3f8050f55dd8f6049b12176098c1" style="height: auto; min-width: 30px; min-height: 30px; width: 600px; left: 178px; top: 575px;">
		<div class="sl-block-content" data-placeholder-tag="p" data-placeholder-text="Text" style="z-index: 18;">
			<p></p>
		</div>
	</div>
</section>'
		];

		yield [
			'
<section data-id="aec4e91743f049f69b2d18fe2c2b1b37">


	<div class="sl-block" data-block-type="text" style="height: auto; width: 801px; left: 80px; top: 177px;" data-block-id="a720a145ac5d1ee1e74695d8956f8771">
		<div class="sl-block-content" data-placeholder-tag="p" data-placeholder-text="Text" style="z-index: 17; color: rgb(5, 44, 80);">
			<p><span style="font-size:0.7em">REDACTED<br>
➀ <strong>REDACTED</strong> ⟶ <span style="color:#008000"><strong>REDACTED</strong></span>REDACTED.</span></p>

			<p><span style="font-size:0.7em">REDACTED<strong>REDACTED</strong>REDACTED<strong>REDACTED</strong>.</span></p>
		</div>
	</div>
	<div class="sl-block" data-block-type="text" style="height: auto; width: 799px; left: 81px; top: 102px;" data-block-id="3368bb07614d592e77fa5a0c427ab594">
		<div class="sl-block-content" data-placeholder-tag="p" data-placeholder-text="Text" style="z-index: 11; color: rgb(5, 44, 80);" dir="ui">
			<p><em><span style="font-size:1.4em">REDACTED</span></em></p>
		</div>
	</div>
	<div class="sl-block" data-block-type="image" style="width: 438px; height: 212px; left: 261px; top: 367px; min-width: 4px; min-height: 4px;" data-block-id="27b90c33906ef6a6fe4543169f6e78fa">
		<div class="sl-block-content" style="z-index: 12;"><img style="left: 0px; top: -119px; width: 438px; height: 438px;" data-natural-width="600" data-natural-height="600" data-crop-x="0" data-crop-y="0.270968" data-crop-width="1" data-crop-height="0.483871" data-lazy-loaded="" data-src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"></div>
	</div>
	<div class="sl-block" data-block-type="text" style="height: auto; width: 600px; left: 180px; top: 594px;" data-block-id="e16a188c77f84e5ce5e76f8662257f63">
		<div class="sl-block-content" data-placeholder-tag="p" data-placeholder-text="Text" style="z-index: 13; color: rgb(5, 44, 80);">
			<p><span style="color:#696969"><span style="font-size:0.7em">REDACTED ☺</span></span></p>
		</div>
	</div>
	<div class="sl-block" data-block-type="line" style="width: auto; height: auto; left: 80px; top: 70px;" data-block-id="2e89108c084c8063e7a27dc09ed654ec">
		<div class="sl-block-content" data-line-x1="-299" data-line-y1="-180" data-line-x2="500" data-line-y2="-180" data-line-color="#052c50" data-line-start-type="none" data-line-end-type="none" style="z-index: 14;" data-line-width="2px" data-line-style="dotted">
			<svg xmlns="http://www.w3.org/2000/svg" version="1.1" preserveaspectratio="xMidYMid" width="799" height="1" viewbox="-299 -180 799 1">
				<line stroke="rgba(0,0,0,0)" stroke-width="15" x1="-299" y1="-180" x2="500" y2="-180"></line>
				<line stroke="#052c50" stroke-width="2" stroke-dasharray="0 3.995" stroke-linecap="round" x1="-299" y1="-180" x2="500" y2="-180"></line>
			</svg>
		</div>
	</div>
	<div class="sl-block" data-block-type="line" style="width: auto; height: auto; left: 82px; top: 663px;" data-block-id="e3b0cf0c03a9d7175211c82a9348f27e">
		<div class="sl-block-content" data-line-x1="-299" data-line-y1="-180" data-line-x2="498" data-line-y2="-180" data-line-color="#052c50" data-line-start-type="none" data-line-end-type="none" style="z-index: 15;" data-line-width="2px" data-line-style="dotted">
			<svg xmlns="http://www.w3.org/2000/svg" version="1.1" preserveaspectratio="xMidYMid" width="797" height="1" viewbox="-299 -180 797 1">
				<line stroke="rgba(0,0,0,0)" stroke-width="15" x1="-299" y1="-180" x2="498" y2="-180"></line>
				<line stroke="#052c50" stroke-width="2" stroke-dasharray="0 3.985" stroke-linecap="round" x1="-299" y1="-180" x2="498" y2="-180"></line>
			</svg>
		</div>
	</div>
	<div class="sl-block" data-block-type="text" style="width: 799px; left: 80px; top: 27px; height: auto;" data-block-id="d8eea0ad9ae5e2d2d3abccf73d605e34">
		<div class="sl-block-content" data-placeholder-tag="h1" data-placeholder-text="Title Text" style="z-index: 16; color: rgb(5, 44, 80); font-size: 52%;">
			<h1 style="text-align: left;">REDACTED</h1>
		</div>
	</div>
</section>',
			'
<section data-id="aec4e91743f049f69b2d18fe2c2b1b37">


	<div class="sl-block" data-block-type="text" style="height: auto; width: 801px; left: 80px; top: 177px;" data-block-id="a720a145ac5d1ee1e74695d8956f8771">
		<div class="sl-block-content" data-placeholder-tag="p" data-placeholder-text="Text" style="z-index: 17; color: rgb(5, 44, 80);">
			<p><span style="font-size:0.7em">REDACTED<br>
➀ <strong>REDACTED</strong> ⟶ <span style="color:#008000"><strong>REDACTED</strong></span>REDACTED.</span></p>

			<p><span style="font-size:0.7em">REDACTED<strong>REDACTED</strong>REDACTED<strong>REDACTED</strong>.</span></p>
		</div>
	</div>
	<div class="sl-block" data-block-type="text" style="height: auto; width: 799px; left: 81px; top: 102px;" data-block-id="3368bb07614d592e77fa5a0c427ab594">
		<div class="sl-block-content" data-placeholder-tag="p" data-placeholder-text="Text" style="z-index: 11; color: rgb(5, 44, 80);" dir="ui">
			<p><em><span style="font-size:1.4em">REDACTED</span></em></p>
		</div>
	</div>
	<div class="sl-block" data-block-type="image" style="width: 438px; height: 212px; left: 261px; top: 367px; min-width: 4px; min-height: 4px;" data-block-id="27b90c33906ef6a6fe4543169f6e78fa">
		<div class="sl-block-content" style="z-index: 12;"><img style="left: 0px; top: -119px; width: 438px; height: 438px;" class="gif" src="http://platform.local/storage/FAKE_PATH.gif"></div>
	</div>
	<div class="sl-block" data-block-type="text" style="height: auto; width: 600px; left: 180px; top: 594px;" data-block-id="e16a188c77f84e5ce5e76f8662257f63">
		<div class="sl-block-content" data-placeholder-tag="p" data-placeholder-text="Text" style="z-index: 13; color: rgb(5, 44, 80);">
			<p><span style="color:#696969"><span style="font-size:0.7em">REDACTED ☺</span></span></p>
		</div>
	</div>
	<div class="sl-block" data-block-type="line" style="width: auto; height: auto; left: 80px; top: 70px;" data-block-id="2e89108c084c8063e7a27dc09ed654ec">
		<div class="sl-block-content" data-line-x1="-299" data-line-y1="-180" data-line-x2="500" data-line-y2="-180" data-line-color="#052c50" data-line-start-type="none" data-line-end-type="none" style="z-index: 14;" data-line-width="2px" data-line-style="dotted">
			<svg xmlns="http://www.w3.org/2000/svg" version="1.1" preserveaspectratio="xMidYMid" width="799" height="1" viewbox="-299 -180 799 1">
				<line stroke="rgba(0,0,0,0)" stroke-width="15" x1="-299" y1="-180" x2="500" y2="-180"></line>
				<line stroke="#052c50" stroke-width="2" stroke-dasharray="0 3.995" stroke-linecap="round" x1="-299" y1="-180" x2="500" y2="-180"></line>
			</svg>
		</div>
	</div>
	<div class="sl-block" data-block-type="line" style="width: auto; height: auto; left: 82px; top: 663px;" data-block-id="e3b0cf0c03a9d7175211c82a9348f27e">
		<div class="sl-block-content" data-line-x1="-299" data-line-y1="-180" data-line-x2="498" data-line-y2="-180" data-line-color="#052c50" data-line-start-type="none" data-line-end-type="none" style="z-index: 15;" data-line-width="2px" data-line-style="dotted">
			<svg xmlns="http://www.w3.org/2000/svg" version="1.1" preserveaspectratio="xMidYMid" width="797" height="1" viewbox="-299 -180 797 1">
				<line stroke="rgba(0,0,0,0)" stroke-width="15" x1="-299" y1="-180" x2="498" y2="-180"></line>
				<line stroke="#052c50" stroke-width="2" stroke-dasharray="0 3.985" stroke-linecap="round" x1="-299" y1="-180" x2="498" y2="-180"></line>
			</svg>
		</div>
	</div>
	<div class="sl-block" data-block-type="text" style="width: 799px; left: 80px; top: 27px; height: auto;" data-block-id="d8eea0ad9ae5e2d2d3abccf73d605e34">
		<div class="sl-block-content" data-placeholder-tag="h1" data-placeholder-text="Title Text" style="z-index: 16; color: rgb(5, 44, 80); font-size: 52%;">
			<h1 style="text-align: left;">REDACTED</h1>
		</div>
	</div>
</section>'
		];
	}
}

class FakeParser extends Parser {
	protected function getStoragePathForImage(string $ext): string {
		return "FAKE_PATH.{$ext}";
	}
}
