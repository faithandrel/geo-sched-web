<?php namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use App\Models\Location;

class LocationRepository extends Repository {
	
	public function model() {
        return Location::class;
    }
}