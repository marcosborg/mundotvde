<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToCrmCardNotesTable extends Migration
{
    public function up()
    {
        Schema::table('crm_card_notes', function (Blueprint $table) {
            $table->unsignedBigInteger('card_id')->nullable();
            $table->foreign('card_id', 'card_fk_10726611')->references('id')->on('crm_cards');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id', 'user_fk_10726612')->references('id')->on('users');
        });
    }
}
