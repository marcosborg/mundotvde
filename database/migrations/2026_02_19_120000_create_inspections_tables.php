<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inspection_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('performer_type', ['driver', 'company']);
            $table->longText('schema_json')->nullable();
            $table->json('required_photo_angles_json')->nullable();
            $table->boolean('requires_signature')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('inspection_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id')->nullable()->index();
            $table->unsignedBigInteger('template_id')->index();
            $table->integer('frequency_days')->default(7);
            $table->string('due_time', 5)->default('09:00');
            $table->integer('grace_hours')->default(24);
            $table->json('reminder_policy_json')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('inspection_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id')->index();
            $table->unsignedBigInteger('template_id')->index();
            $table->unsignedBigInteger('assigned_user_id')->nullable()->index();
            $table->dateTime('period_start');
            $table->dateTime('period_end');
            $table->dateTime('due_at');
            $table->enum('status', ['pending', 'in_progress', 'submitted', 'reviewed', 'rejected', 'overdue'])->default('pending');
            $table->enum('generated_by', ['scheduler', 'manual'])->default('scheduler');
            $table->timestamps();

            $table->index(['assigned_user_id', 'status']);
            $table->index(['vehicle_id', 'due_at']);
        });

        Schema::create('inspection_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id')->index();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->text('summary_notes')->nullable();
            $table->string('signature_path')->nullable();
            $table->json('location_json')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('inspection_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('submission_id')->index();
            $table->enum('angle', ['front', 'rear', 'left', 'right', 'front_left', 'front_right', 'interior', 'odometer', 'other']);
            $table->string('file_disk')->default('inspections_private');
            $table->string('file_path');
            $table->string('mime', 100)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('sha256', 64)->nullable();
            $table->dateTime('captured_at')->nullable();
            $table->dateTime('uploaded_at')->nullable();
            $table->json('meta_json')->nullable();
            $table->timestamps();

            $table->index(['submission_id', 'angle']);
        });

        Schema::create('inspection_defects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id')->index();
            $table->unsignedBigInteger('created_from_submission_id')->nullable()->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('severity', ['non_critical', 'critical'])->default('non_critical');
            $table->enum('status', ['open', 'in_progress', 'resolved'])->default('open');
            $table->unsignedBigInteger('created_by_user_id')->nullable()->index();
            $table->unsignedBigInteger('assigned_to_user_id')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('inspection_defect_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('defect_id')->index();
            $table->unsignedBigInteger('photo_id')->index();
            $table->timestamps();

            $table->unique(['defect_id', 'photo_id']);
        });

        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->enum('platform', ['android', 'ios']);
            $table->string('token', 512);
            $table->dateTime('last_seen_at')->nullable();
            $table->dateTime('revoked_at')->nullable();
            $table->timestamps();

            $table->unique(['platform', 'token']);
            $table->index(['user_id', 'revoked_at']);
        });

        Schema::create('inspection_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('push_enabled')->default(true);
            $table->json('default_reminder_policy_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_settings');
        Schema::dropIfExists('device_tokens');
        Schema::dropIfExists('inspection_defect_photos');
        Schema::dropIfExists('inspection_defects');
        Schema::dropIfExists('inspection_photos');
        Schema::dropIfExists('inspection_submissions');
        Schema::dropIfExists('inspection_assignments');
        Schema::dropIfExists('inspection_schedules');
        Schema::dropIfExists('inspection_templates');
    }
};
