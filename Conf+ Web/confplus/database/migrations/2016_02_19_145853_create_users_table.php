<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('email');
            $table->string('username');
            $table->binary('password');
            $table->char('title', 10)->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('dob')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->boolean('verified')->default(false)->nullable();
            $table->string('fb_id')->nullable();
            $table->string('linkedin_id')->nullable();
            $table->boolean('active')->nullable();
            $table->boolean('upgraded')->nullable();
            $table->boolean('review')->default(false)->nullable();
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
