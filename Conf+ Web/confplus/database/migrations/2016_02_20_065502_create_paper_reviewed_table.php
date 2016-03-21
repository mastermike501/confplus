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
            $table->string('title');
            $table->timestamp('publish_date');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->primary(['email', 'title', 'publish_date']);
            $table->foreign(['title', 'publish_date'])->references(['title', 'publish_date'])->on('papers')->onDelete('cascade');

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
