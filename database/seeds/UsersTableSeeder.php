<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = [];
        for($i=0; $i<10; $i++) {
        	$faker  = Faker\Factory::create();
        	$name 	= strtolower($faker->firstName); 
        	$rows[] = [
        		'name' => $name,
		        'email' => $name.'@test.com', 
        		'password' => bcrypt($name),
        	];
        }

        DB::table('users')->insert($rows);
    }
}
