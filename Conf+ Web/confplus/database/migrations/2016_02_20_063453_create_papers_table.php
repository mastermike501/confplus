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
            $table->increments('paper_id');
            $table->string('title');
            $table->timestamp('publish_date');
            $table->timestamp('latest_submit_date')->useCurrent();
            $table->string('status')->default('unreviewed');
            $table->boolean('accept')->nullable();
            $table->integer('final_rate')->nullable();
            $table->string('url');
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            //$table->timestamps();

            $table->unique(['title', 'publish_date']);
            $table->unique('url');

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
