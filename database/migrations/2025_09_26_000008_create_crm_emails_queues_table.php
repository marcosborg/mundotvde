<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrmEmailsQueuesTable extends Migration
{
    public function up()
    {
        Schema::create('crm_emails_queues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('to');
            $table->string('cc')->nullable();
            $table->string('subject');
            $table->longText('body_html')->nullable();
            $table->string('status')->nullable();
            $table->string('error')->nullable();
            $table->datetime('scheduled_at')->nullable();
            $table->datetime('sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
