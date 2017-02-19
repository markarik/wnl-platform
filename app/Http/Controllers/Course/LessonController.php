<?php

namespace App\Http\Controllers\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LessonController extends Controller
{

	public function index() {
		return view('course.lesson');
	}
}
