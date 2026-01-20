<?php

declare(strict_types=1);

use App\Enums\UserField;
use App\Traits\HasMappedFields;

class UserDTO
{
    use HasMappedFields;

    public const Name           = UserField::Name->value;
    public const Email          = UserField::Email->value;
    public const FullName       = UserField::FullName->value;
    public const Role           = UserField::Role->value;
    public const Address        = UserField::Address->value;
    public const City           = UserField::City->value;
    public const Country        = UserField::Country->value;
    public const PhoneNumber    = UserField::PhoneNumber->value;

    public static array $requestMap = [
        UserField::Name->value          => self::Name,
        UserField::Email->value         => self::Email,
        UserField::Role->value          => self::Role,
        UserField::FullName->value      => self::FullName,
        UserField::Address->value       => self::Address,
        UserField::City->value          => self::City,
        UserField::Country->value       => self::Country,
        UserField::PhoneNumber->value   => self::PhoneNumber,
    ];

    public function __construct(
        public string $name,
        public string $email,
        public ?string $role = null,
        public ?string $fullName = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $country = null,
        public ?string $phoneNumber = null
    ) {}
}
