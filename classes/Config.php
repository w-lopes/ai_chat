<?php

namespace classes;

final class Config
{
    private static array $config = [];

    public static function get(string $attr, mixed $default = null): mixed
    {
        self::load();
        return self::$config[$attr] ?? $default;
    }

    private static function load(): void
    {
        if (!empty(self::$config)) {
            return;
        }

        self::$config = json_decode(
            preg_replace('/\/\/[\w\s\.]+/m', '', file_get_contents(__DIR__ . '/../config.jsonc')),
            true
        );
    }
}
