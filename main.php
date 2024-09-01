<?php

use App\Commands\AddCommand;
use App\Commands\CalculateCommand;
use App\Commands\DeleteCommand;
use App\Commands\UpdateCommand;
use Symfony\Component\Console\Application;

require_once 'vendor/autoload.php';

$app = new Application();

$app->add(new AddCommand());
$app->add(new UpdateCommand());
$app->add(new DeleteCommand());
$app->add(new CalculateCommand());

try {
    $app->run();
} catch (Exception $e) {
}
