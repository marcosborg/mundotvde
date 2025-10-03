<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrmFormFieldsTable extends Migration
{
    public function up()
    {
        Schema::create('crm_form_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('label');
            $table->string('type')->nullable();
            $table->boolean('required')->default(0)->nullable();
            $table->string('help_text')->nullable();
            $table->string('placeholder')->nullable();
            $table->string('default_value')->nullable();
            $table->boolean('is_unique')->default(0)->nullable();
            $table->integer('min_value')->nullable();
            $table->integer('max_value')->nullable();
            $table->longText('options_json')->nullable();
            $table->integer('position')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
