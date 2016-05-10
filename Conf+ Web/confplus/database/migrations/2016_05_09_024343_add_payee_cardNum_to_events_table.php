<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayeeCardNumToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('payee');
            $table->string('cardNum');
            $table->foreign(['payee', 'cardNum'])->references(['email', 'card#'])->on('billings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['payee', 'cardNum']);
            $table->dropColumn('payee');
            $table->dropColumn('cardNum');
        });
    }
}
