<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_record', function (Blueprint $table) {
            $table->integer('event_id')->unsigned();
            $table->string('title');
            $table->string('ticket_name');
            $table->string('class');
            $table->string('type');
            $table->integer('venue_id')->unsigned();
            $table->string('room_name');
            $table->string('seat_num');
            $table->string('email')->nullable();
            $table->timestamps();

            $table->primary(['event_id', 'title', 'ticket_name', 'class', 'type', 'venue_id', 'room_name', 'seat_num']);
            $table->foreign(['event_id', 'title', 'ticket_name', 'class', 'type'])->references(['event_id', 'title', 'name', 'class', 'type'])->on('tickets')->onDelete('cascade');
            $table->foreign(['venue_id', 'room_name', 'seat_num'])->references(['venue_id', 'name', 'seat_num'])->on('seats')->onDelete('cascade');
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
        Schema::drop('ticket_record');
    }
}
