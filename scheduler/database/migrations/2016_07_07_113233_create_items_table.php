<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('content');
            
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                    ->references('id')->on('users');
                    
            $table->integer('item_id')->unsigned()->nullable();
            $table->foreign('item_id')
                    ->references('id')->on('items');
            
            $table->double('lat');
            $table->double('long');
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('items');
    }
}
