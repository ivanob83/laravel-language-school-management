<?php

namespace App\Traits;

trait HasMappedFields
{
    /**
     * Put back mapped frontend field for DTO field
     *
     * @param string $field Standard DTO field ('NAME', 'EMAIL')
     * @return string Frontend field matching
     */
    public static function mapped(string $field): string
    {
        if (!property_exists(static::class, 'requestMap')) {
            throw new \LogicException('DTO must have a static $requestMap property.');
        }

        $requestMap = static::$requestMap;

        $consts = (new \ReflectionClass(static::class))->getConstants();
        $standard = $consts[strtoupper($field)] ?? $field;

        $flip = array_flip($requestMap); 
        return $flip[$standard] ?? $standard;
    }

    /**
     * Put back mapped array for DTO
     * @return array<string, mixed> Mapped array
     */
    public static function toMappedArray(): array
    {
        if (!property_exists(static::class, 'requestMap')) {
            throw new \LogicException('DTO must have a static $requestMap property.');
        }

        $mapped = [];
        foreach (static::$requestMap as $frontendField => $dtoConst) {
            $property = static::mapped($frontendField);
            $mapped[$frontendField] = $this->{$property} ?? null;
        }

        return $mapped;
    }

    /**
     * Create DTO from request data
     *
     * @param array<string, mixed> $data Request data
     * @return array<string, mixed> Mapped array
     */
    public static function arrayFromRequest(array $data): array
    {
        $result = [];

        foreach (static::$requestMap as $frontendKey => $dtoProp) {
            // mapira frontend polje na DTO property
            $result[$dtoProp] = $data[$frontendKey] ?? null;
        }

        return $result;
    }
}
