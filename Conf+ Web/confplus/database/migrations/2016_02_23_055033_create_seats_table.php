<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->integer('venue_id')->unsigned();
            $table->string('name');
            $table->string('seat_num');
            $table->timestamps();

            $table->primary(['venue_id', 'name', 'seat_num']);
            $table->foreign(['venue_id', 'name'])->references(['venue_id', 'name'])->on('rooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('seats');
    }
}
