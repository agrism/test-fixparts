<?php

require dirname(__DIR__) . '/vendor/autoload.php';

try {
    $application = new \App\Application();
    $application->run();
} catch (Exception $e) {
    echo $e->getMessage(), PHP_EOL;
}