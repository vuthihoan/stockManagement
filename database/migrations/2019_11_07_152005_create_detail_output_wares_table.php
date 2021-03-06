<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailOutputWaresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_output_wares', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('detail_output_quantity')->nullable();
            $table->integer('detail_estimate_quantity')->nullable();
            $table->integer('detail_output_amount')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('zone_id')->nullable();
            $table->unsignedBigInteger('outputWare_id');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('outputWare_id')->references('id')->on('output_wares')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_output_wares');
    }
}
