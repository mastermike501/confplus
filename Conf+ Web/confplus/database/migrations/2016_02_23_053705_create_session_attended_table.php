<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionAttendedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('session_attended', function (Blueprint $table) {
            $table->string('email');
            $table->integer('event_id')->unsigned();
            $table->string('title');
            $table->string('seat_num')->nullable();
            $table->timestamps();

            $table->primary(['email', 'event_id', 'title']);
            $table->foreign('email')->references('email')->on('users')->onDelete('cascade');
            $table->foreign(['event_id', 'title'])->references(['event_id', 'title'])->on('sessions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('session_attended');
    }
}
