<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inspection_schedules', function (Blueprint $table) {
            $table->json('routine_config')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('inspection_schedules', function (Blueprint $table) {
            $table->dropColumn('routine_config');
        });
    }
};

