<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePapersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('papers', function (Blueprint $table) {
            $table->string('title');
            $table->timestamp('publish_date')->useCurrent();
            $table->timestamp('latest_submit_date');
            $table->string('status')->default('unreviewed');
            $table->boolean('accept');
            $table->string('url');
            $table->timestamps();

            $table->primary(['title', 'publish_date']);
            $table->unique('url');

            $table->nullable('accept');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('papers');
    }
}
