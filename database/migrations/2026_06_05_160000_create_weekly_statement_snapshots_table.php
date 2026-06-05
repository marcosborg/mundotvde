<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tvde_weeks', function (Blueprint $table) {
            if (!Schema::hasColumn('tvde_weeks', 'status')) {
                $table->string('status', 20)->default('open')->after('number');
            }

            if (!Schema::hasColumn('tvde_weeks', 'locked_at')) {
                $table->timestamp('locked_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('tvde_weeks', 'locked_by')) {
                $table->foreignId('locked_by')->nullable()->after('locked_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('tvde_weeks', 'reopened_at')) {
                $table->timestamp('reopened_at')->nullable()->after('locked_by');
            }

            if (!Schema::hasColumn('tvde_weeks', 'reopened_by')) {
                $table->foreignId('reopened_by')->nullable()->after('reopened_at')->constrained('users')->nullOnDelete();
            }
        });

        if (!Schema::hasTable('weekly_statement_snapshots')) {
            Schema::create('weekly_statement_snapshots', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tvde_week_id')->constrained('tvde_weeks')->cascadeOnDelete();
                $table->foreignId('activity_launch_id')->constrained('activity_launches')->cascadeOnDelete();
                $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
                $table->foreignId('vehicle_id')->nullable()->constrained('vehicle_items')->nullOnDelete();
                $table->string('vehicle_plate')->nullable();
                $table->string('driver_name')->nullable();
                $table->json('data_json');
                $table->string('pdf_path');
                $table->string('pdf_hash', 64);
                $table->unsignedInteger('version')->default(1);
                $table->dateTime('locked_at');
                $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['activity_launch_id', 'version']);
                $table->index(['tvde_week_id', 'version']);
                $table->index(['driver_id', 'locked_at']);
            });
        }

        $adminRole = Role::where('title', 'Admin')->first();
        foreach (['tvde_week_close', 'tvde_week_reopen'] as $title) {
            $permission = Permission::withTrashed()->firstOrCreate(['title' => $title]);
            if (method_exists($permission, 'trashed') && $permission->trashed()) {
                $permission->restore();
            }

            if ($adminRole) {
                $adminRole->permissions()->syncWithoutDetaching([$permission->id]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_statement_snapshots');

        Schema::table('tvde_weeks', function (Blueprint $table) {
            foreach (['reopened_by', 'locked_by'] as $column) {
                if (Schema::hasColumn('tvde_weeks', $column)) {
                    $table->dropConstrainedForeignId($column);
                }
            }

            foreach (['reopened_at', 'locked_at', 'status'] as $column) {
                if (Schema::hasColumn('tvde_weeks', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
