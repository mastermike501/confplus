<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->integer('venue_id')->unsigned();
            $table->string('name');
            $table->string('type');
            $table->integer('capacity')->unsigned();
            $table->timestamps();

            $table->primary(['venue_id', 'name']);
            $table->foreign('venue_id')->references('venue_id')->on('venues')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('rooms');
    }
}
