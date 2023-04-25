<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleEventsTable extends Migration
{
    public function up()
    {
        Schema::create('vehicle_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->longText('description')->nullable();
            $table->datetime('date');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
