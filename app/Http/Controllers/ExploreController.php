<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Repositories\TagRepository;
use App\Repositories\ItemRepository;
use App\Services\Emoji\EmojiParser;

class ExploreController extends Controller
{
	private $tagRepository, $itemRepository;

    /**
     * @param TagRepository $tagRepo 
     * @param ItemRepository $itemRepo 
     * @return type
     */
    public function __construct(TagRepository $tagRepo,
                                ItemRepository $itemRepo)
    {
    	$this->tagRepository  = $tagRepo;
        $this->itemRepository = $itemRepo;
    }


    public function index()
    {
    	$emojis = $this->tagRepository->all(['name'])->toArray();

    	$emojis = array_map(function ($emoji) {
			return EmojiParser::shortnameToUnicode(":".$emoji['name'].":");
		}, $emojis);

        $emojiArray = [];

    	return response()->json(array_chunk($emojis, 3));
    }

    public function getItemsForEmoji(Request $request)
    {
        $emoji = $request->get('emoji');
        $emoji = EmojiParser::parse($emoji)[0];

        $tag = $this->tagRepository->findBy('name', $emoji);

        return response()->json($tag->items);
    }
}
