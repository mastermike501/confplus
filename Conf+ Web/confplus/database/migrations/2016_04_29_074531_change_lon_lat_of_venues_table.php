<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLonLatOfVenuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->decimal('longitude', 9, 6)->change();
            $table->decimal('latitude', 9, 6)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('venues', function (Blueprint $table) {
          $table->decimal('longitude', 6, 3)->change();
          $table->decimal('latitude', 6, 2)->change();
        });
    }
}
