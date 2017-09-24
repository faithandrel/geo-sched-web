<?php namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use App\Services\EmojiParser;
use App\Models\Tag;
use App\Models\Item;
use Carbon\Carbon;

class TagRepository extends Repository {
	
	public function model() {
        return Tag::class;
    }

    /**
     * Stores emoji tags for item from given strings
     * @param Item $item 
     * @param Array $strings 
     * @return type
     */
    public function createFromArray(Item $item, Array $strings) {
    	$tags = [];

    	//Extract emoji tags from strings
    	foreach ($strings as $value) {
    		$tags = array_merge($tags, EmojiParser::parse($value));
    	}

    	if(count($tags) < 1) {
    		return;
    	}

        $tags = array_unique($tags);
    	$existingTags = Tag::whereIn('name', $tags)->get();

    	$newTags = $tags;
    	//Get new tags
    	if($existingTags->count() > 0) {
    		$newTags = array_diff($tags, $existingTags->pluck('name')->toArray());
    	}

    	$newTags = array_map(function ($tag) {
            $created = Carbon::now();
			return ['name' => $tag, 'created_at' => $created, 'updated_at' => $created];
		}, $newTags);

    	Tag::insert($newTags);

    	if(count($newTags) > 0) {
    		$newTagModels = Tag::whereIn('name', array_column($newTags, 'name'))->get();
    		$existingTags = $existingTags->merge($newTagModels);
    	}
        
        if($existingTags->count() > 0) {
            $item->tags()->sync($existingTags);
        }
    }
}