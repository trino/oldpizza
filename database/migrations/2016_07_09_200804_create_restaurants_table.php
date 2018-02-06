<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->string('email');
            $table->integer('phone');
            $table->string('cuisine');
            $table->string('website');
            $table->text('description');
            $table->string('logo');
            $table->integer('is_delivery');
            $table->integer('is_pickup');
            $table->integer('max_delivery_distance');
            $table->integer('delivery_fee');
            $table->integer('minimum');
            $table->integer('is_complete');
            $table->integer('lastorder_id');
            $table->integer('franchise');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('restaurants');
    }
}
