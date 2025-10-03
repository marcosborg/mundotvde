<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrmCardActivitiesTable extends Migration
{
    public function up()
    {
        Schema::create('crm_card_activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type')->nullable();
            $table->longText('meta_json')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
