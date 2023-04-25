<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleEventWarningTimesTable extends Migration
{
    public function up()
    {
        Schema::create('vehicle_event_warning_times', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('days');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
