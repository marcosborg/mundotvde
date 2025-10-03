<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrmFormsTable extends Migration
{
    public function up()
    {
        Schema::create('crm_forms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('status')->nullable();
            $table->string('confirmation_message')->nullable();
            $table->string('redirect_url')->nullable();
            $table->string('notify_emails')->nullable();
            $table->boolean('create_card_on_submit')->default(0)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
