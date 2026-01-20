<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LanguageClassAssignmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'language_class_id' => $this->language_class_id,
            'student_id' => $this->student_id,
            // Student info
            'student' => $this->whenLoaded('student', fn() => [
                'id' => $this->student->id,
                'name' => $this->student->name,
                'full_name' => $this->student->full_name,
                'email' => $this->student->email,
            ]),

            // Class info with professor
            'language_class' => $this->whenLoaded('languageClass', fn() => [
                'id' => $this->languageClass->id,
                'title' => $this->languageClass->title,
                'schedule_time' => $this->languageClass->schedule_time->format('Y-m-d H:i:s'),
                'status' => $this->languageClass->status,
                'professor' => [
                    'id' => $this->languageClass->professor->id,
                    'name' => $this->languageClass->professor->name,
                    'full_name' => $this->languageClass->professor->full_name,
                    'email' => $this->languageClass->professor->email,
                ],
            ]),

            // Assignment status (from pivot)
            'status' => $this->status,

            // Pivot timestamps
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
