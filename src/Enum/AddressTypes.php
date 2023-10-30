<?php

namespace App\Enum;

enum AddressTypes : string
{
    case BUSINESS       = 'BUSINESS';
    case PRIVATE        = 'PRIVATE';
    case CORRESPONDANCE = 'CORRESPONDANCE';

    public static function getType(string $type): self
    {
        return constant('self::$type');
    }

}