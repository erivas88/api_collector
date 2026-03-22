<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $tables = \Illuminate\Support\Facades\DB::connection()->getSchemaBuilder()->getTableListing();
    echo "Database: " . \Illuminate\Support\Facades\DB::connection()->getDatabaseName() . "\n";
    echo "Host: " . \Illuminate\Support\Facades\DB::connection()->getConfig('host') . "\n";
    echo "Tables found: \n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
