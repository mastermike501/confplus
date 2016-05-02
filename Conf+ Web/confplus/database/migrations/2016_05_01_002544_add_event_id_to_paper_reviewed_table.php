<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEventIdToPaperReviewedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paper_reviewed', function (Blueprint $table) {
            $table->integer('event_id')->unsigned()->after('paper_id');
            $table->foreign('event_id')->references('event_id')->on('events')->onDelete('cascade');
        });
        DB::unprepared('ALTER TABLE `paper_reviewed` DROP PRIMARY KEY, ADD PRIMARY KEY (`email`, `paper_id`, `event_id`)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paper_reviewed', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropColumn('event_id');

        });
        DB::unprepared('ALTER TABLE `paper_reviewed` DROP PRIMARY KEY, ADD PRIMARY KEY (`email`, `paper_id`)');
    }
}
