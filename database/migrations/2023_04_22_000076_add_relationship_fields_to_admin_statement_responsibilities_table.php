<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToAdminStatementResponsibilitiesTable extends Migration
{
    public function up()
    {
        Schema::table('admin_statement_responsibilities', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->foreign('driver_id', 'driver_fk_8372342')->references('id')->on('drivers');
        });
    }
}