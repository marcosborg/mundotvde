<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    public function up()
    {
        Schema::create('driver_message_templates', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->string('title');
            $t->longText('body')->nullable();
            $t->boolean('active')->default(false);
            $t->timestamps();
        });
        foreach (['own_vehicle' => 'Viatura própria', 'company_rental' => 'Viatura alugada nossa', 'external_rental' => 'Viatura alugada de terceiros'] as $key => $title) {
            DB::table('driver_message_templates')->insert(['key' => $key, 'title' => $title, 'created_at' => now(), 'updated_at' => now()]);
        }
        DB::table('driver_message_templates')->insert(['key' => 'welcome', 'title' => 'Bem-vindo ao MundoTVDE', 'body' => "Olá {nome},\n\nA sua conta MundoTVDE já está ativa. Aceda à sua área pessoal em {login_url}.\nSe ainda não definiu a sua palavra-passe, utilize a opção de recuperação no ecrã de login.\n\nSe tiver dúvidas, contacte a nossa equipa.\nBem-vindo ao MundoTVDE!", 'active' => true, 'created_at' => now(), 'updated_at' => now()]);
        Schema::create('driver_contract_versions', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('driver_id')->index();
            $t->unsignedBigInteger('user_id')->index();
            $t->unsignedInteger('version');
            $t->string('type');
            $t->string('title');
            $t->longText('body');
            $t->json('snapshot');
            $t->string('content_hash', 64);
            $t->unsignedBigInteger('created_by');
            $t->timestamp('accepted_at')->nullable();
            $t->unsignedBigInteger('accepted_by')->nullable();
            $t->string('accepted_ip', 45)->nullable();
            $t->text('accepted_user_agent')->nullable();
            $t->string('acceptance_text')->nullable();
            $t->timestamp('superseded_at')->nullable();
            $t->timestamps();
            $t->unique(['driver_id', 'version']);
        });
        Schema::create('driver_welcome_deliveries', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('driver_id')->index();
            $t->unsignedBigInteger('sent_by');
            $t->string('recipient');
            $t->string('subject');
            $t->text('body');
            $t->string('mailer');
            $t->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('driver_welcome_deliveries');
        Schema::dropIfExists('driver_contract_versions');
        Schema::dropIfExists('driver_message_templates');
    }
};
