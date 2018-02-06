<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('hours', function (Blueprint $table) {
                $table->integer('restraunt_id');
                $table->time('monday_open');
                $table->time('monday_close');
                $table->time('tuesday_open');
                $table->time('tuesday_close');
                $table->time('wednesday_open');
                $table->time('wednesday_close');
                $table->time('thursday_open');
                $table->time('thursday_close');
                $table->time('friday_open');
                $table->time('friday_close');
                $table->time('saturday_open');
                $table->time('saturday_close');
                $table->time('sunday_open');
                $table->time('sunday_close');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('hours');
    }
}
