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
            $table->string('title');
            $table->timestamp('publish_date');
            $table->string('tag_name');
            $table->timestamps();

            $table->primary(['title', 'publish_date', 'tag_name']);
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
        Schema::drop('papers_tag');
    }
}
