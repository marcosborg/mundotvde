<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$controller = new App\Http\Controllers\Admin\TvdeDriverManagementController;
$view = $controller->launchAllActivities(153);
$data = $view->getData();
$drivers = $data["drivers"];
echo "drivers: " . $drivers->count() . PHP_EOL;
$first = $drivers->first();
if ($first) { echo "first driver: {$first->name}, total=".($first->results["uber_activities"]["earnings_one"]+$first->results["uber_activities"]["earnings_two"]+$first->results["uber_activities"]["earnings_three"]+$first->results["bolt_activities"]["earnings_one"]+$first->results["bolt_activities"]["earnings_two"]+$first->results["bolt_activities"]["earnings_three"]).PHP_EOL; }

