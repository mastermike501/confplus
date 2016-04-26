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
            $table->integer('venue_id')->unsigned()->nullable();
            $table->text('description');
            $table->string('url');
            $table->string('poster_url')->nullable();
            $table->timestamp('paper_deadline')->nullable();
            $table->string('language')->default('EN');
            $table->string('reminder')->nullable();
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            //$table->timestamps();

            $table->foreign('venue_id')->references('venue_id')->on('venues')->onDelete('cascade');
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
