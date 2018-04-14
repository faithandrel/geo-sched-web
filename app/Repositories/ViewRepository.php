<?php namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use App\Models\View;
use App\Models\User;
use App\Models\Item;

class ViewRepository extends Repository {
	
	public function model() {
        return View::class;
    }

    public function firstOrCreate(User $user, Item $item) {
    	$view = View::firstOrCreate(['user_id' => $user->id, 'item_id' => $item->id]);

    	return $view;
    }

    public function getItemViewCount(Item $item) {
    	$views = $this->findAllBy('item_id', $item->id);

    	return $views->count();
    }
}