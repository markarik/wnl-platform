<?php

namespace App\Http\Controllers\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JavaScript;

class CourseController extends Controller
{
	public function index() {
		return view('course.course');
	}
}
