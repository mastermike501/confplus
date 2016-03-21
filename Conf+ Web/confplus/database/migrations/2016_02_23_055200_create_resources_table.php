<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->integer('venue_id')->unsigned();
            $table->string('room_name');
            $table->string('name');
            $table->string('type');
            $table->integer('number')->unsigned();
            $table->timestamps();

            $table->primary(['venue_id', 'room_name', 'name']);
            $table->foreign(['venue_id', 'room_name'])->references(['venue_id', 'name'])->on('rooms')->onDelete('cascade');
            // $table->foreign('venue_id')->references('venue_id')->on('rooms')->onDelete('cascade');
            // $table->foreign('room_name')->references('room_name')->on('rooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('resources');
    }
}
