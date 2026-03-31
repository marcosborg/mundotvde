<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->integer('position')->nullable()->after('icon');
        });

        $activities = DB::table('activities')->orderBy('id')->get(['id']);
        $position = 1;

        foreach ($activities as $activity) {
            DB::table('activities')
                ->where('id', $activity->id)
                ->update(['position' => $position++]);
        }
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
