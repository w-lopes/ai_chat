<?php

namespace classes;

use DirectoryIterator;
use interfaces\Command as InterfacesCommand;
use ReflectionClass;

class Command
{
    public static array $commands = [];

    public static function loadCommands(): array
    {
        if (!empty(self::$commands)) {
            return self::$commands;
        };

        foreach (new DirectoryIterator(__DIR__ . DS . '..' . DS . 'commands') as $fileInfo) {
            if($fileInfo->isDot()) {
                continue;
            }

            $fileInfo->getFilename();
            $class = '\\commands\\' . str_replace('.php', '', $fileInfo->getFilename());
            $instance = new $class();

            self::$commands[$instance::class] = [
                'instance' => $instance,
                'class' => $instance::class,
            ] + self::getCommandAttributes($instance);
        }

        return self::$commands;
    }

    private static function getCommandAttributes(InterfacesCommand $instance): array
    {
        $ret = [];
        $class = new ReflectionClass($instance);
        $attributes = $class->getAttributes();
        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();
            $ret[strtolower(preg_replace('/.*\\\\/', '', $instance::class))] = $instance->value;
        }
        return $ret;
    }
}
