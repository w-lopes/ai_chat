<?php

namespace classes;

use classes\Config;
use classes\Llama;

class Main
{
    public static function checkRequirements(): void
    {
        if (!Llama::fileExists()) {
            error(Config::get('llamafile') . ' file does not exist! Download it from https://github.com/Mozilla-Ocho/llamafile');
        }

        if (!Llama::isRunning()) {
            error('Llamafile service is not running!');
        }
    }

    public static function run(): void
    {
        do {
            info('  - Enter your command here:');
            $stdin = fopen('php://stdin', 'r');
            $line = trim(fgets($stdin));

            if (empty($line)) {
                continue;
            }

            $aiResponse = Llama::sendPrompt($line);
            info('>>> ' . self::parseResponse($aiResponse));
        } while (true);
    }

    private static function parseResponse(array $aiResponse): string
    {
        if (!$aiResponse['is_custom_command']) {
            return $aiResponse['response'];
        }
        return trim(Command::$commands[$aiResponse['custom_command_class']]['instance']->execute());
    }
}
