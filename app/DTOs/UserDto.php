<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\UserField;
use Illuminate\Http\Request;

final readonly class UserDTO
{

    public function __construct(
        public string $name,
        public string $email,
        public string $role,
        public ?string $full_name = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $country = null,
        public ?string $phone_number = null
    ) {}

    /** 
     * Create UserDTO from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data[UserField::Name->value],
            email: $data[UserField::Email->value],
            role: $data[UserField::Role->value],
            full_name: $data[UserField::FullName->value] ?? null,
            address: $data[UserField::Address->value] ?? null,
            city: $data[UserField::City->value] ?? null,
            country: $data[UserField::Country->value] ?? null,
            phone_number: $data[UserField::PhoneNumber->value] ?? null,
        );
    }

    /**
     *  Convert UserDTO to array suitable for database update 
     */
    public function toDbArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'full_name' => $this->full_name,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'phone_number' => $this->phone_number,
        ];
    }

    /**
     *  Convert UserDTO to array suitable for API response with mapped field names from Enums/UserField 
     */
    public function toResponseArray(): array
    {
        return [
            UserField::Name->value => $this->name,
            UserField::Email->value => $this->email,
            UserField::Role->value => $this->role,
            UserField::FullName->value => $this->full_name,
            UserField::Address->value => $this->address,
            UserField::City->value => $this->city,
            UserField::Country->value => $this->country,
            UserField::PhoneNumber->value => $this->phone_number,
        ];
    }
}
