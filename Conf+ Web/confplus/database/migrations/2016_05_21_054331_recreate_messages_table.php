<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('messages');
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('message_id');
            $table->string('sender_email');
            $table->integer('conversation_id')->unsigned();
            $table->timestamp('date')->useCurrent();
            $table->text('content');
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            //$table->timestamps();

            $table->unique(['sender_email', 'conversation_id', 'date']);
            $table->foreign('sender_email')->references('email')->on('users')->onDelete('cascade');
            $table->foreign('conversation_id')->references('conversation_id')->on('conversations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
