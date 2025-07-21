<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdoptionRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'animal' => new AnimalResource($this->whenLoaded('animal')),
             'status' => $this->status,

            'family_members_count' => $this->family_members_count,
            'address' => $this->address,
            'has_children' => $this->has_children,
            'children_ages' => json_decode($this->children_ages, true),

            'job_title' => $this->job_title,
            'company_name' => $this->company_name,
            'work_hours_per_day' => $this->work_hours_per_day,
            'work_type' => $this->work_type,

            'housing_type' => $this->housing_type,
            'is_rented' => $this->is_rented,
            'landlord_name' => $this->landlord_name,
            'landlord_phone' => $this->landlord_phone,
            'landlord_allows_pets' => $this->landlord_allows_pets,
            'has_garden' => $this->has_garden,

            'has_patience' => $this->has_patience,
            'can_handle_issues' => $this->can_handle_issues,
            'hours_with_pet_daily' => $this->hours_with_pet_daily,
            'someone_home_24_7' => $this->someone_home_24_7,
            'can_be_with_pet_when_sick' => $this->can_be_with_pet_when_sick,

            'agreed_to_terms' => $this->agreed_to_terms,
            'notes' => $this->notes,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }        
}
