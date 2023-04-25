<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleItemsTable extends Migration
{
    public function up()
    {
        Schema::create('vehicle_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('year');
            $table->string('license_plate');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
