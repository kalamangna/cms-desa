<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

Illuminate\Support\Facades\Event::listen(\Illuminate\Console\Events\CommandStarting::class, function ($event) {
    if ($event->command === 'backup:run') {
        if ($event->input->hasParameterOption('--filename')) {
            $current = $event->input->getParameterOption('--filename');
            $event->input->setOption('filename', 'TEST-' . $current);
            echo "Intercepted: " . $event->input->getOption('filename') . "\n";
        }
    }
});
$kernel->call('backup:run', ['--filename' => 'only-db.zip', '--only-db' => true]);
