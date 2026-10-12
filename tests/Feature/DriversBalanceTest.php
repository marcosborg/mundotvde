<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\DriversBalanceController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DriversBalanceTest extends TestCase
{
    public function test_balances_only_load_unpaid_launches_and_their_active_operators(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Gate::before(fn ($user = null) => true);

        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('operation_id')->nullable();
            $table->softDeletes();
        });
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
        });
        Schema::create('activity_launches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->boolean('paid');
            foreach (['rent', 'management', 'insurance', 'fuel', 'garage', 'management_fee', 'tolls', 'others', 'refund'] as $column) {
                $table->decimal($column, 12, 2)->nullable();
            }
            $table->softDeletes();
        });
        Schema::create('activity_per_operators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_launch_id');
            $table->decimal('net', 12, 2);
            $table->decimal('taxes', 12, 2);
            $table->softDeletes();
        });

        DB::table('drivers')->insert([['id' => 1], ['id' => 2]]);
        DB::table('activity_launches')->insert([
            ['id' => 1, 'driver_id' => 1, 'paid' => 0, 'rent' => 10, 'management_fee' => 2.5, 'refund' => 3],
            ['id' => 2, 'driver_id' => 1, 'paid' => 1, 'rent' => 999, 'management_fee' => 0, 'refund' => 0],
            ['id' => 3, 'driver_id' => 2, 'paid' => 1, 'rent' => 999, 'management_fee' => 0, 'refund' => 0],
        ]);
        DB::table('activity_per_operators')->insert([
            ['activity_launch_id' => 1, 'net' => 100, 'taxes' => 20, 'deleted_at' => null],
            ['activity_launch_id' => 1, 'net' => 50, 'taxes' => 5, 'deleted_at' => null],
            ['activity_launch_id' => 1, 'net' => 999, 'taxes' => 0, 'deleted_at' => '2026-01-01 00:00:00'],
            ['activity_launch_id' => 2, 'net' => 999, 'taxes' => 0, 'deleted_at' => null],
        ]);

        $drivers = (new DriversBalanceController)->index()->getData()['drivers'];

        $this->assertCount(1, $drivers);
        $this->assertEquals(1, $drivers->first()->id);
        $this->assertEqualsWithDelta(115.5, $drivers->first()->balance, 0.001);
        $this->assertCount(1, $drivers->first()->activity_launches);
        $this->assertCount(2, $drivers->first()->activity_launches->first()->activityPerOperators);
    }
}
