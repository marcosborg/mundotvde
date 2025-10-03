<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToCrmFormFieldsTable extends Migration
{
    public function up()
    {
        Schema::table('crm_form_fields', function (Blueprint $table) {
            $table->unsignedBigInteger('form_id')->nullable();
            $table->foreign('form_id', 'form_fk_10726541')->references('id')->on('crm_forms');
        });
    }
}
