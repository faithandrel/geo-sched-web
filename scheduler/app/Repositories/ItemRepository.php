<?php namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use App\Models\Dispute;

class ItemRepository extends Repository {
	
	public function model() {
        return Item::class;
    }
}