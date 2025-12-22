
<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$selectedMonth = App\Models\TvdeMonth::orderByDesc("id")->first();
$selectedMonth = App\Models\TvdeMonth::with(["weeks.activityLaunches.driver.card","weeks.activityLaunches.driver.operation","weeks.activityLaunches.activityPerOperators.tvde_operator"])->find($selectedMonth->id);
echo "Month {$selectedMonth->id} weeks={$selectedMonth->weeks->count()}" . PHP_EOL;
foreach ($selectedMonth->weeks as $week) {
    echo "week {$week->id} num={$week->number} launches={$week->activityLaunches->count()}" . PHP_EOL;
}

