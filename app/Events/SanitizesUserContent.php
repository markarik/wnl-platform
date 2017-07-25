<?php

namespace App\Events;


trait SanitizesUserContent
{
	public function sanitize($content)
	{
		$content = strip_tags($content);

		return $content;
	}
}
