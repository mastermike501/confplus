<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventAttendedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_attended', function (Blueprint $table) {
            $table->string('email');
            $table->integer('event_id')->unsigned();
            $table->string('role');
            $table->timestamps();

            $table->primary(['email', 'event_id', 'role']);
            $table->foreign('email')->references('email')->on('users')->onDelete('cascade');
            $table->foreign('event_id')->references('event_id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('event_attended');
    }
}