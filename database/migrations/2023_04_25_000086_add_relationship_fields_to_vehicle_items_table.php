<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToVehicleItemsTable extends Migration
{
    public function up()
    {
        Schema::table('vehicle_items', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->foreign('driver_id', 'driver_fk_8382538')->references('id')->on('drivers');
            $table->unsignedBigInteger('vehicle_brand_id')->nullable();
            $table->foreign('vehicle_brand_id', 'vehicle_brand_fk_8382539')->references('id')->on('vehicle_brands');
            $table->unsignedBigInteger('vehicle_model_id')->nullable();
            $table->foreign('vehicle_model_id', 'vehicle_model_fk_8382540')->references('id')->on('vehicle_models');
        });
    }
}
