<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Illuminate\Support\Facades\Hash;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('facebook_id')->unique()->nullable();
            $table->string('google_id')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('admin')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('users')->insert([
            ['name' => env('ADMIN_NAME'), 'email' => env('ADMIN_EMAIL'), 'password' => Hash::make(env('ADMIN_PASS')), 'admin' => 1, 'nick' => env('ADMIN_NICK')],
            ['name' => env('DEFAULT_NAME'), 'email' => env('DEFAULT_EMAIL'), 'password' => Hash::make(env('DEFAULT_PASS')), 'admin' => 0, 'nick' => env('DEFAULT_NICK')]            
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
