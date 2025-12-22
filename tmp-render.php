<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$request = Illuminate\Http\Request::create("/", "GET");
$controller = new App\Http\Controllers\Admin\TvdeDriverManagementController;
$response = $controller->ajax($request);
$html = $response->render();
echo (substr_count($html, "Semana 50") ? "contains Semana 50\n" : "no Semana 50\n");
echo "weeks tabs: " . substr_count($html, "Semana ") . PHP_EOL;
file_put_contents("tmp-render.html", $html);

