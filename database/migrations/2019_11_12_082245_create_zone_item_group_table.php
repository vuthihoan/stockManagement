<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZoneItemGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zone_item_group', function (Blueprint $table) {
            $table->unsignedBigInteger('zone_id');
            $table->unsignedBigInteger('item_group_id');

            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('cascade');
            $table->foreign('item_group_id')->references('id')->on('item_groups')->onDelete('cascade');
            $table->primary(['zone_id', 'item_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zone_item_group');
    }
}
