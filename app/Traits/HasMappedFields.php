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
}
