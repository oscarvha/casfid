<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

$envFile = dirname(__DIR__).'/.env';

if (method_exists(Dotenv::class, 'bootEnv') && file_exists($envFile)) {
    (new Dotenv())->bootEnv($envFile);
}

if ($_SERVER['APP_DEBUG'] ?? false) {
    umask(0000);
}

