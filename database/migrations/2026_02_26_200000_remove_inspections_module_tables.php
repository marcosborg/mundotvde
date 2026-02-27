<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('inspection_defect_photos');
        Schema::dropIfExists('inspection_photos');
        Schema::dropIfExists('inspection_submissions');
        Schema::dropIfExists('inspection_assignments');
        Schema::dropIfExists('inspection_schedules');
        Schema::dropIfExists('inspection_defects');
        Schema::dropIfExists('inspection_templates');
        Schema::dropIfExists('inspection_settings');
        Schema::dropIfExists('device_tokens');

        Schema::enableForeignKeyConstraints();

        if (Schema::hasTable('permissions')) {
            DB::table('permissions')
                ->where('title', 'like', 'inspections.%')
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback: inspection module was intentionally removed.
    }
};
