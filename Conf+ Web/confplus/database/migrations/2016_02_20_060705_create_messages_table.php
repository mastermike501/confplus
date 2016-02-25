<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->string('sender_email');
            $table->string('receiver_email');
            $table->text('content');
            $table->timestamp('date')->useCurrent();
            $table->timestamps();

            $table->primary(['sender_email', 'receiver_email', 'date']);
            $table->foreign('sender_email')->references('email')->on('users')->onDelete('cascade');
            $table->foreign('receiver_email')->references('email')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('messages');
    }
}
