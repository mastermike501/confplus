<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaperReviewedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paper_reviewed', function (Blueprint $table) {
            $table->string('email');
            $table->integer('paper_id')->unsigned();
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->primary(['email', 'paper_id']);
            $table->foreign('email')->references('email')->on('users')->onDelete('cascade');
            $table->foreign('paper_id')->references('paper_id')->on('papers')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('paper_reviewed');
    }
}
