<?php

use Illuminate\Database\Seeder;

class ViewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = [];
        for($i=2; $i<10; $i++) {
        	$rows[] = [
        		'item_id' => 26,
		        'user_id' => $i, 
        	];
        }

        DB::table('views')->insert($rows);
    }
}
