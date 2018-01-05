<?php namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use App\Models\User;

class UserRepository extends Repository {
	
	public function model() {
        return User::class;
    }
}