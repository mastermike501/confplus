<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment', function (Blueprint $table) {
            $table->string('email');
            $table->increments('payment_id');
            $table->string('type');
            $table->decimal('amount', 5, 2);
            $table->timestamp('payment_date')->useCurrent();
            $table->timestamps();

            $table->primary(['email', 'payment_id']);
            $table->unique('payment_id');
            $table->foreign('email')->references('email')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('payment');
    }
}
