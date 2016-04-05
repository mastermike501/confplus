<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->integer('event_id')->unsigned();
            $table->string('title');
            $table->string('name');
            $table->string('class');
            $table->string('type');
            $table->decimal('price', 5, 2)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->integer('quantity')->unsigned();
            $table->integer('num_purchased')->unsigned()->default(0);
            $table->timestamps();

            $table->primary(['event_id', 'title', 'name', 'class', 'type']);
            $table->foreign(['event_id', 'title'])->references(['event_id', 'title'])->on('sessions')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tickets');
    }
}
