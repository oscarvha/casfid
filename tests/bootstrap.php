<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

$projectDir = dirname(__DIR__);
$env = $_SERVER['APP_ENV'] ?? 'test';

$dotenv = new Dotenv();

if ($env === 'test' && file_exists($projectDir.'/.env.test')) {
    $dotenv->load($projectDir.'/.env.test');
} elseif (file_exists($projectDir.'/.env')) {
    $dotenv->load($projectDir.'/.env');
}

if ($_SERVER['APP_DEBUG'] ?? false) {
    umask(0000);
}

