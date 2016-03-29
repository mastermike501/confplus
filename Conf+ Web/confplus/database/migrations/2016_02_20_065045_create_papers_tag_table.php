<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePapersTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('papers_tag', function (Blueprint $table) {
            $table->integer('paper_id')->unsigned();
            $table->string('tag_name');
            $table->timestamps();

            $table->primary(['paper_id', 'tag_name']);
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
        Schema::drop('papers_tag');
    }
}
