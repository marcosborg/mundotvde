<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrmStagesTable extends Migration
{
    public function up()
    {
        Schema::create('crm_stages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('position')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_won')->default(0)->nullable();
            $table->boolean('is_lost')->default(0)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
