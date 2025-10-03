<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToCrmCardsTable extends Migration
{
    public function up()
    {
        Schema::table('crm_cards', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id', 'category_fk_10726591')->references('id')->on('crm_categories');
            $table->unsignedBigInteger('stage_id')->nullable();
            $table->foreign('stage_id', 'stage_fk_10726592')->references('id')->on('crm_stages');
            $table->unsignedBigInteger('form_id')->nullable();
            $table->foreign('form_id', 'form_fk_10726594')->references('id')->on('crm_forms');
            $table->unsignedBigInteger('assigned_to_id')->nullable();
            $table->foreign('assigned_to_id', 'assigned_to_fk_10726602')->references('id')->on('users');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->foreign('created_by_id', 'created_by_fk_10726603')->references('id')->on('users');
        });
    }
}
