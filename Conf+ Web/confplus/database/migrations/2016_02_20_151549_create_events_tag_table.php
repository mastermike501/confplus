<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events_tag', function (Blueprint $table) {
            $table->integer('event_id')->unsigned();
            $table->string('tag_name');
            $table->timestamps();

            $table->primary(['event_id', 'tag_name']);
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
        Schema::drop('events_tag');
    }
}
