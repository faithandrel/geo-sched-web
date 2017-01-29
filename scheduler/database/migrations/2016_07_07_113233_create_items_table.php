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
            $table->string('title')->collation('utf8mb4_general_ci');;
            $table->text('content')->collation('utf8mb4_general_ci');;
            
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                    ->references('id')->on('users');
                    
            $table->integer('item_id')->unsigned()->nullable();
            $table->foreign('item_id')
                    ->references('id')->on('items');
            
            $table->double('latitude');
            $table->double('longitude');
            
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
