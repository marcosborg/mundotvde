<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrmStageEmailsTable extends Migration
{
    public function up()
    {
        Schema::create('crm_stage_emails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('to_emails')->nullable();
            $table->longText('bcc_emails')->nullable();
            $table->string('subject');
            $table->longText('body_template')->nullable();
            $table->boolean('send_on_enter')->default(0)->nullable();
            $table->boolean('send_on_exit')->default(0)->nullable();
            $table->integer('delay_minutes')->nullable();
            $table->boolean('is_active')->default(0)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
