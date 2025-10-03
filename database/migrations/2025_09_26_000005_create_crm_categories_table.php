<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrmCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('crm_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('color')->nullable();
            $table->integer('position')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
