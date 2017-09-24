<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Repositories\TagRepository;
use LaravelEmojiOne;

class ExploreController extends Controller
{
	private $tagRepository;

	/**
	 * @param TagRepository $tagRepo 
	 * @return type
	 */
    public function __construct(TagRepository $tagRepo)
    {
    	$this->tagRepository = $tagRepo;
    }


    public function index()
    {
    	$emojis = $this->tagRepository->all(['name'])->toArray();

    	$emojis = array_map(function ($emoji) {
			return LaravelEmojiOne::shortnameToUnicode(":".$emoji['name'].":");
		}, $emojis);

        $emojiArray = [];

    	return response()->json(array_chunk($emojis, 3));
    }
}
