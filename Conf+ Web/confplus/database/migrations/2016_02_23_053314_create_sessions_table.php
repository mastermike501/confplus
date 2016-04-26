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
            $table->string('speaker_email')->nullable();
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->integer('venue_id')->unsigned()->nullable();
            $table->string('room_name')->nullable();
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            //$table->timestamps();

            $table->primary(['event_id', 'title']);
            $table->foreign('event_id')->references('event_id')->on('events')->onDelete('cascade');
            $table->foreign('speaker_email')->references('email')->on('users')->onDelete('cascade');
            $table->foreign(['venue_id', 'room_name'])->references(['venue_id', 'name'])->on('rooms')->onDelete('cascade');
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
