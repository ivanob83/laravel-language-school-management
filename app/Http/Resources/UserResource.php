<?php

namespace App\Http\Resources;

use App\Enums\UserField;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            UserField::Name->value => $this->name,
            UserField::FullName->value => $this->full_name,
            UserField::Email->value => $this->email,
            UserField::Address->value => $this->address,
            UserField::Role->value => $this->role,
            UserField::City->value => $this->city,
            UserField::Country->value => $this->country,
            UserField::PhoneNumber->value => $this->phone_number,
            'email_verified_at' => $this->email_verified_at,
            'taught_classes' => $this->whenLoaded('taughtClasses'), // for professor
            'enrolled_classes' => $this->whenLoaded('enrolledClasses'), // for student
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
