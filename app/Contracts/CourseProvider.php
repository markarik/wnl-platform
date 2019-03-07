<?php

namespace App\Contracts;

class CourseProvider {

	static $DEFAULT_COURSE_ID = 1;

	public function getCourseId() {
		return static::$DEFAULT_COURSE_ID;
	}
}
