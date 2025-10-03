<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToCrmCardActivitiesTable extends Migration
{
    public function up()
    {
        Schema::table('crm_card_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('card_id')->nullable();
            $table->foreign('card_id', 'card_fk_10726618')->references('id')->on('crm_cards');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->foreign('created_by_id', 'created_by_fk_10726621')->references('id')->on('users');
        });
    }
}
