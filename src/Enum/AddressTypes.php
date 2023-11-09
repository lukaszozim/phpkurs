<?php

namespace App\Enum;

enum AddressTypes : string
{
    case BUSINESS       = 'BUSINESS';
    case PRIVATE        = 'PRIVATE';
    case CORRESPONDENCE = 'CORRESPONDENCE';

    public static function getType(string $type): self
    {
        return constant('self::$type');
    }

    public static function getAllValues(): array {
        $values = [];
        $reflection = new \ReflectionClass(self::class);
        foreach ($reflection->getConstants() as $constantName => $constantValue) {
            $values[] = $constantValue;
        }
        return $values;
    }

}