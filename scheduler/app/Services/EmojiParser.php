<?php 

namespace App\Services;

use LaravelEmojiOne;

class EmojiParser {

	public static function parse($value) {
		$matches = [];
		preg_match_all('/\:(.*?)\:/', LaravelEmojiOne::toShort($value), $matches);

		return array_unique($matches[1]);
		//return ['hello'];
	}

}