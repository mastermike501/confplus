<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('email');
            $table->string('username');
            $table->binary('password');
            $table->char('title', 10);
            $table->string('first_name');
            $table->string('last_name');
            $table->date('dob');
            $table->string('street');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->boolean('verified')->default(false);
            $table->string('fb_id');
            $table->string('linkedin_id');
            $table->timestamps();

            $table->primary('email');
            $table->unique('username');
            $table->unique('fb_id');
            $table->unique('linkedin_id');
        });
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
