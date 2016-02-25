<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_tag', function (Blueprint $table) {
            $table->string('email');
            $table->string('tag_name');
            $table->timestamps();

            $table->primary(['email', 'tag_name']);
            $table->foreign('email')->references('email')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_tag');
    }
}
