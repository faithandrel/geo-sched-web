<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('names', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                    ->references('id')->on('users');
                    
            $table->integer('item_id')->unsigned()->nullable();
            $table->foreign('item_id')
                    ->references('id')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('names');
    }
}
