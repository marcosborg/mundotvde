<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminStatementResponsibilitiesTable extends Migration
{
    public function up()
    {
        Schema::create('admin_statement_responsibilities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('contract_number');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}