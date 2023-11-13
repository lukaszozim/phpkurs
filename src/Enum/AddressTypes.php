<?php

namespace App\Enum;

enum AddressTypes : string
{
    case BUSINESS       = 'BUSINESS';
    case PRIVATE        = 'PRIVATE';
    case CORRESPONDENCE = 'CORRESPONDENCE';

    public static function getType(string $type): self
    {
        return match ($type) {
            'BUSINESS' => self::BUSINESS,
            'PRIVATE' => self::PRIVATE,
            'CORRESPONDENCE' => self::CORRESPONDENCE,
            default => throw new \InvalidArgumentException("Invalid AddressType: $type"),
        };
//        return constant('self::$type');
    }

    public static function values(): array {
    return [
        self::BUSINESS->name,
        self::PRIVATE->name,
        self::CORRESPONDENCE->name,
    ];
}


    public static function getAllValues(): array {
//        $values = [];
//        $reflection = new \ReflectionClass(self::class);
//        foreach ($reflection->getConstants() as $constantName => $constantValue) {
//            $values[] = $constantValue;
//        }

//        $values = AddressTypes::values();
//        return $values;
        return self::cases();
    }

    public static function toArray(AddressTypes $enum): array
    {
        return ['value' => $enum->value];
    }

}