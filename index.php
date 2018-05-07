<?php
/**
 * Autosolid.eu
 *
 * Project: Autosolid web app
 * Created by maciejwasiak.com
 */

require_once __DIR__ . '/vendor/autoload.php';

$debug = ($_SERVER['SERVER_NAME'] === '127.0.0.1');
$env = App\Application::ENV_PROD;

if($debug)
{
    include_once 'debug.php';
    $env = App\Application::ENV_DEV;
}

$app = new App\Application($env);
$app->run();