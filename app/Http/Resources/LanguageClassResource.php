<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LanguageClassResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'schedule_time' => $this->schedule_time->format('Y-m-d H:i:s'),
            'status' => $this->status,

            // Professor info
            'professor' => $this->whenLoaded('professor', fn() => [
                'id' => $this->professor->id,
                'name' => $this->professor->name,
                'full_name' => $this->professor->full_name,
                'email' => $this->professor->email,
            ]),

            // Students info with pivot status
            'students' => $this->whenLoaded('students', fn() => $this->students->map(fn($student) => [
                'id' => $student->id,
                'name' => $student->name,
                'full_name' => $student->full_name,
                'email' => $student->email,
                'status' => $student->pivot->status, // assigned | passed | failed
            ])),
            'students_count' => $this->students_count ?? 0,

            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
