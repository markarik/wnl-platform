<?php

namespace App\Events;


trait SanitizesUserContent
{
	public function sanitize($content)
	{
		$content = str_limit($content, static::TEXT_LIMIT);
		$content = strip_tags($content);

		return $content;
	}
}
