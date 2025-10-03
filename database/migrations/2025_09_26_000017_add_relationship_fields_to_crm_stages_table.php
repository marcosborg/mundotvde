<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToCrmStagesTable extends Migration
{
    public function up()
    {
        Schema::table('crm_stages', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id', 'category_fk_10726491')->references('id')->on('crm_categories');
            $table->unsignedBigInteger('auto_assign_to_user_id')->nullable();
            $table->foreign('auto_assign_to_user_id', 'auto_assign_to_user_fk_10726497')->references('id')->on('users');
        });
    }
}
