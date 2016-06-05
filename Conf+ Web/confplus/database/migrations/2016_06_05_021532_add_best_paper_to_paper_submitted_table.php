<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBestPaperToPaperSubmittedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paper_submitted', function (Blueprint $table) {
            $table->string('best_paper')->nullable()->after('status')->default('false');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paper_submitted', function (Blueprint $table) {
            $table->dropColumn('best_paper');
        });
    }
}
