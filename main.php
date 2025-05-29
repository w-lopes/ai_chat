<?php

use classes\Command;
use classes\Main;

define('DS', DIRECTORY_SEPARATOR);

spl_autoload_register(function(string $className): void
{
    $file = __DIR__ . '\\' . $className . '.php';
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
    if (file_exists($file)) {
        include $file;
    }
});

function error(string $message): void
{
    echo $message . PHP_EOL;
    exit;
}

function info(string $message): void
{
    echo $message . PHP_EOL;
}

Main::checkRequirements();
Command::loadCommands();
Main::run();
