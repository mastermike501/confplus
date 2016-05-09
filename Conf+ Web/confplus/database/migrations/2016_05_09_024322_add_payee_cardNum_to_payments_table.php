<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayeeCardNumToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payee')->after('amount');
            $table->string('cardNum')->after('payee');
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
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['payee', 'cardNum']);
            $table->dropColumn('payee');
            $table->dropColumn('cardNum');
        });
    }
}
