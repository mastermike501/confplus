<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNullableToTicketRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_record', function (Blueprint $table) {
            $table->integer('venue_id')->unsigned()->nullable()->change();
            $table->string('room_name')->nullable()->change();
            $table->integer('seat_num')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_record', function (Blueprint $table) {
            $table->integer('venue_id')->unsigned()->change();
            $table->string('room_name')->change();
            $table->integer('seat_num')->change();
        });
    }
}
