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
            $table->string('speaker_email');
            $table->timestamps();

            $table->primary(['email', 'event_id', 'title', 'speaker_email']);
            $table->foreign('email')->references('email')->on('users')->onDelete('cascade');
            $table->foreign(['event_id', 'title', 'speaker_email'])->references(['event_id', 'title', 'speaker_email'])->on('sessions')->onDelete('cascade');
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
