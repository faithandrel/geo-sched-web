<?php 

namespace App\Services;

use ChristofferOK\LaravelEmojiOne\LaravelEmojiOneFacade;

class EmojiParser {

	public static function parse($value) {
		$matches = [];
		preg_match_all('/\:(.*?)\:/', LaravelEmojiOneFacade::toShort($value), $matches);

		return array_unique($matches[1]);
		//return ['hello'];
	}

}