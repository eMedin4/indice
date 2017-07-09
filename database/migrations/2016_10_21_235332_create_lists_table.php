<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 48);
            $table->string('description', 500)->nullable();
            $table->boolean('ordered')->default(0);
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('list_movie', function (Blueprint $table) {
            $table->integer('movie_id')->unsigned();
            $table->foreign('movie_id')->references('id')->on('movies');
            $table->integer('list_id')->unsigned();
            $table->foreign('list_id')->references('id')->on('lists')->onDelete('cascade');
            $table->smallInteger('order');
        });

        /*DB::table('lists')->insert([
            ['id' => 1,  'name' => 'Prov1', 'ordered' => 0, 'user_id' => 2],       
            ['id' => 2,  'name' => 'Prov2', 'ordered' => 0, 'user_id' => 2],       
            ['id' => 3,  'name' => 'Prov3', 'ordered' => 0, 'user_id' => 2],       
            ['id' => 4,  'name' => 'Prov4', 'ordered' => 0, 'user_id' => 2],       
            ['id' => 5,  'name' => 'Prov5', 'ordered' => 0, 'user_id' => 2],       
            ['id' => 6,  'name' => 'Prov6', 'ordered' => 0, 'user_id' => 2],       
        ]);*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('list_movie');
        Schema::dropIfExists('lists');
    }
}
