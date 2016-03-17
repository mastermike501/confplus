<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVenuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->increments('venue_id');
            $table->string('name');
            $table->string('type');
            $table->boolean('has_room');
            $table->string('street');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->decimal('longitude', 6, 3);
            $table->decimal('latitude', 6, 2);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('venues');
    }
}
