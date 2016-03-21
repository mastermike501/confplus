<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->integer('event_id')->unsigned();
            $table->string('title');
            $table->string('speaker_email');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->timestamps();

            $table->primary(['event_id', 'title', 'speaker_email']);
            $table->foreign('event_id')->references('event_id')->on('events')->onDelete('cascade');
            $table->foreign('speaker_email')->references('email')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sessions');
    }
}
