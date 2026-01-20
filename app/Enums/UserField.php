<?php

namespace App\Enums;

/**
 * Enum representing user fields in requests which in feature can be changed and will be mapped dynamically.
 */
enum UserField: string
{
    case Name = 'name';
    case Email = 'email';
    case Role = 'role';
    case FullName = 'full_name';
    case Address = 'address';
    case City = 'city';
    case Country = 'country';
    case PhoneNumber = 'phone_number';
}