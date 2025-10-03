<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToCrmStageEmailsTable extends Migration
{
    public function up()
    {
        Schema::table('crm_stage_emails', function (Blueprint $table) {
            $table->unsignedBigInteger('stage_id')->nullable();
            $table->foreign('stage_id', 'stage_fk_10726502')->references('id')->on('crm_stages');
        });
    }
}
