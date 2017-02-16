<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Lib\SlideParser\Parser;

class UploadSlidesController extends Controller
{
    public function index(){
    	return view('admin.upload-slides');
	}

	public function handle(Parser $parser, Request $request) {
		$parser->parse($request->get('slides'));
		return view('admin.upload-slides');
	}
}
