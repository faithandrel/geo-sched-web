<?php 

namespace App\Services\Emoji;

use LaravelEmojiOne;
use Emojione\Emojione;

class EmojiParser {

	public static function parse($value) {
		$matches = [];
		preg_match_all('/\:(.*?)\:/', LaravelEmojiOne::toShort($value), $matches);

		return array_unique($matches[1]);
		//return ['hello'];
	}

	public static function shortnameToUnicode($str) {
		return Emojione::shortnameToUnicode($str);
	}

}