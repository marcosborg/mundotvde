<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToCrmFormSubmissionsTable extends Migration
{
    public function up()
    {
        Schema::table('crm_form_submissions', function (Blueprint $table) {
            $table->unsignedBigInteger('form_id')->nullable();
            $table->foreign('form_id', 'form_fk_10726557')->references('id')->on('crm_forms');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id', 'category_fk_10726558')->references('id')->on('crm_categories');
            $table->unsignedBigInteger('created_card_id')->nullable();
            $table->foreign('created_card_id', 'created_card_fk_10726564')->references('id')->on('crm_cards');
        });
    }
}
