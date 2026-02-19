<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inspection_assignments', function (Blueprint $table) {
            $table->integer('grace_hours')->default(24)->after('due_at');
            $table->json('reminder_policy_json')->nullable()->after('grace_hours');
        });
    }

    public function down(): void
    {
        Schema::table('inspection_assignments', function (Blueprint $table) {
            $table->dropColumn(['grace_hours', 'reminder_policy_json']);
        });
    }
};
