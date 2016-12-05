<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;

class ChapterController extends Controller
{

	public function index($chapterId) {

		$chapter = Chapter::with('sections')->find( $chapterId );

		return view('chapter', [
			'chapter' => $chapter
		]);
	}
}
