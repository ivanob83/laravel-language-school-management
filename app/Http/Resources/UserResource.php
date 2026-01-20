<?php

namespace App\Http\Resources;

use App\DTOs\UserDTO;
use App\Enums\UserField;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $dto = UserDTO::fromArray($request->all());
        return array_merge(
            ['id' => $this->id],
            $dto->toResponseArray(),
            [
                'email_verified_at' => $this->email_verified_at,
                'taught_classes' => $this->whenLoaded('taughtClasses'), // for professor
                'enrolled_classes' => $this->whenLoaded('enrolledClasses'), // for student
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
            ]
        );
    }
}
