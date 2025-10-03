<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrmCardsTable extends Migration
{
    public function up()
    {
        Schema::create('crm_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('source')->nullable();
            $table->string('priority')->nullable();
            $table->string('status')->nullable();
            $table->string('lost_reason')->nullable();
            $table->datetime('won_at')->nullable();
            $table->datetime('closed_at')->nullable();
            $table->datetime('due_at')->nullable();
            $table->integer('position')->nullable();
            $table->longText('fields_snapshot_json')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
