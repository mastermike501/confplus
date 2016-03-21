<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('event_id');
            $table->string('name');
            $table->string('type');
            $table->timestamp('from_date');
            $table->timestamp('to_date');
            $table->text('description');
            $table->string('url');
            $table->string('poster_url')->nullable();
            $table->timestamp('paper_deadline')->nullable();
            $table->string('language')->default('EN');
            $table->string('reminder')->nullable();
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
        Schema::drop('events');
    }
}
