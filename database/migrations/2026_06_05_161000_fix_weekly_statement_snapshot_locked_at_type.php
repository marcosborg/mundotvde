<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('weekly_statement_snapshots') && Schema::hasColumn('weekly_statement_snapshots', 'locked_at')) {
            DB::statement('ALTER TABLE weekly_statement_snapshots MODIFY locked_at DATETIME NOT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('weekly_statement_snapshots') && Schema::hasColumn('weekly_statement_snapshots', 'locked_at')) {
            DB::statement('ALTER TABLE weekly_statement_snapshots MODIFY locked_at TIMESTAMP NOT NULL');
        }
    }
};
