<?php

namespace Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait CreatesInspectionTestSchema
{
    protected function bootInspectionTestDatabase(): void
    {
        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::dropAllTables();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('user_id');
        });

        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('license_plate')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('vehicle_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->string('license_plate')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inspection_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('performer_type');
            $table->text('schema_json')->nullable();
            $table->text('required_photo_angles_json')->nullable();
            $table->boolean('requires_signature')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('inspection_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('template_id');
            $table->integer('frequency_days')->default(7);
            $table->string('due_time', 5)->default('09:00');
            $table->integer('grace_hours')->default(24);
            $table->text('reminder_policy_json')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('inspection_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('template_id');
            $table->unsignedBigInteger('assigned_user_id')->nullable();
            $table->dateTime('period_start');
            $table->dateTime('period_end');
            $table->dateTime('due_at');
            $table->integer('grace_hours')->default(24);
            $table->text('reminder_policy_json')->nullable();
            $table->string('status')->default('pending');
            $table->string('generated_by')->default('scheduler');
            $table->timestamps();
        });

        Schema::create('inspection_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->text('summary_notes')->nullable();
            $table->string('signature_path')->nullable();
            $table->text('location_json')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();
        });

        Schema::create('inspection_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('submission_id');
            $table->string('angle');
            $table->string('file_disk')->nullable();
            $table->string('file_path')->nullable();
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('sha256')->nullable();
            $table->dateTime('captured_at')->nullable();
            $table->dateTime('uploaded_at')->nullable();
            $table->text('meta_json')->nullable();
            $table->timestamps();
        });

        Schema::create('inspection_defects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('created_from_submission_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('severity')->default('non_critical');
            $table->string('status')->default('open');
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('assigned_to_user_id')->nullable();
            $table->timestamps();
        });

        Schema::create('inspection_defect_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('defect_id');
            $table->unsignedBigInteger('photo_id');
            $table->timestamps();
        });

        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('platform');
            $table->string('token');
            $table->dateTime('last_seen_at')->nullable();
            $table->dateTime('revoked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('inspection_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('push_enabled')->default(false);
            $table->text('default_reminder_policy_json')->nullable();
            $table->timestamps();
        });
    }
}
