<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToCrmEmailsQueuesTable extends Migration
{
    public function up()
    {
        Schema::table('crm_emails_queues', function (Blueprint $table) {
            $table->unsignedBigInteger('stage_email_id')->nullable();
            $table->foreign('stage_email_id', 'stage_email_fk_10726515')->references('id')->on('crm_stage_emails');
            $table->unsignedBigInteger('card_id')->nullable();
            $table->foreign('card_id', 'card_fk_10726516')->references('id')->on('crm_cards');
        });
    }
}
