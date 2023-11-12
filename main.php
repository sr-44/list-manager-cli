<?php

use App\Cli;
use Symfony\Component\Console\Application;

require_once 'vendor/autoload.php';

$app = new Application();

$app->add(new Cli());

try {
    $app->run();
} catch (Exception $e) {
}
