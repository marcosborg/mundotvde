<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToVehicleEventsTable extends Migration
{
    public function up()
    {
        Schema::table('vehicle_events', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_event_type_id')->nullable();
            $table->foreign('vehicle_event_type_id', 'vehicle_event_type_fk_8382510')->references('id')->on('vehicle_event_types');
            $table->unsignedBigInteger('vehicle_event_warning_time_id')->nullable();
            $table->foreign('vehicle_event_warning_time_id', 'vehicle_event_warning_time_fk_8382511')->references('id')->on('vehicle_event_warning_times');
            $table->unsignedBigInteger('vehicle_item_id')->nullable();
            $table->foreign('vehicle_item_id', 'vehicle_item_fk_8384417')->references('id')->on('vehicle_items');
        });
    }
}
