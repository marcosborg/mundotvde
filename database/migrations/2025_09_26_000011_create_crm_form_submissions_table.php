<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrmFormSubmissionsTable extends Migration
{
    public function up()
    {
        Schema::create('crm_form_submissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('submitted_at')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referer')->nullable();
            $table->longText('utm_json')->nullable();
            $table->longText('data_json')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
