<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStandTvdePagesTable extends Migration
{
    public function up()
    {
        Schema::create('stand_tvde_pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->longText('text')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}