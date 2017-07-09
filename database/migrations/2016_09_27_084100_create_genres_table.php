<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->integer('id')->unsigned()->primary();
            $table->string('name');
        });

        Schema::create('genre_movie', function (Blueprint $table) {
            $table->integer('movie_id')->unsigned();
            $table->foreign('movie_id')->references('id')->on('movies');
            $table->integer('genre_id')->unsigned();
            $table->foreign('genre_id')->references('id')->on('genres');
        });

/*        DB::table('genres')->insert([
            ['id' => 12, 'name' => 'Aventura'], 
            ['id' => 14, 'name' => 'Fantasía'],
            ['id' => 16, 'name' => 'Animación'],
            ['id' => 18, 'name' => 'Drama'],
            ['id' => 27, 'name' => 'Terror'],
            ['id' => 28, 'name' => 'Acción'],
            ['id' => 35, 'name' => 'Comedia'],
            ['id' => 36, 'name' => 'Historia'],
            ['id' => 53, 'name' => 'Suspense'],
            ['id' => 80, 'name' => 'Crimen'],
            ['id' => 99, 'name' => 'Documental'],
            ['id' => 878, 'name' => 'Ciencia Ficción'],
            ['id' => 9648, 'name' => 'Misterio'],
            ['id' => 10402, 'name' => 'Música'],
            ['id' => 10749, 'name' => 'Romance'],
            ['id' => 10751, 'name' => 'Familia'],
            ['id' => 10752, 'name' => 'Guerra']
        ]);*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('genre_movie');
        Schema::drop('genres');
    }
}
