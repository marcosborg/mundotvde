<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToCrmFormsTable extends Migration
{
    public function up()
    {
        Schema::table('crm_forms', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id', 'category_fk_10726529')->references('id')->on('crm_categories');
        });
    }
}
