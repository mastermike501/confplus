<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_type', function (Blueprint $table) {
            $table->integer('event_id')->unsigned();
            $table->string('name');
            $table->decimal('price', 5, 2)->nullable();
            $table->text('description');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->integer('quantity')->unsigned();
            $table->integer('num_purchased')->unsigned()->default(0);
            $table->timestamps();

            $table->primary(['event_id', 'name']);
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
        Schema::drop('ticket_type');
    }
}
