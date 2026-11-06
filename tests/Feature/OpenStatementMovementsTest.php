<?php

namespace Tests\Feature;

use App\Models\ActivityLaunch;
use App\Services\WeeklyStatements\WeeklyStatementSnapshotService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OpenStatementMovementsTest extends TestCase
{
    public function test_open_statements_include_old_unpaid_items_and_exclude_paid_or_unsent_items(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('activity_launches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->boolean('send')->default(false);
            $table->boolean('paid')->default(false);
            foreach (['rent', 'management', 'insurance', 'fuel', 'tolls', 'garage', 'management_fee', 'others', 'refund'] as $column) {
                $table->decimal($column, 12, 2)->nullable();
            }
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('activity_per_operators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_launch_id');
            $table->unsignedBigInteger('tvde_operator_id')->nullable();
            $table->decimal('net', 12, 2);
            $table->decimal('taxes', 12, 2);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('tvde_operators', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('activity_launches')->insert([
            [
                'id' => 1,
                'driver_id' => 10,
                'send' => 1,
                'paid' => 0,
                'created_at' => Carbon::now()->subMonths(6),
                'updated_at' => Carbon::now()->subMonths(6),
            ],
            [
                'id' => 2,
                'driver_id' => 10,
                'send' => 1,
                'paid' => 1,
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ],
            [
                'id' => 3,
                'driver_id' => 10,
                'send' => 0,
                'paid' => 0,
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ],
            [
                'id' => 4,
                'driver_id' => 20,
                'send' => 1,
                'paid' => 0,
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ],
        ]);
        DB::table('activity_per_operators')->insert([
            'activity_launch_id' => 1,
            'tvde_operator_id' => null,
            'net' => 100,
            'taxes' => 20,
            'created_at' => Carbon::now()->subMonths(6),
            'updated_at' => Carbon::now()->subMonths(6),
        ]);

        $statementIds = ActivityLaunch::where('driver_id', 10)
            ->openStatements()
            ->orderBy('id')
            ->pluck('id')
            ->all();

        $this->assertSame([1], $statementIds);

        $statement = app(WeeklyStatementSnapshotService::class)->refreshOpenMovements([
            'amounts' => [
                'movements_60_days' => [['id' => 2]],
                'balance' => 999,
            ],
        ], 10);

        $this->assertArrayNotHasKey('movements_60_days', $statement['amounts']);
        $this->assertSame([1], collect($statement['amounts']['open_movements'])->pluck('id')->all());
        $this->assertEqualsWithDelta(80.0, $statement['amounts']['balance'], 0.001);
    }
}
