<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnimalResource extends JsonResource
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
            'category' => new CategoryResource($this->whenLoaded('category')),
            'breed' => new BreedResource($this->whenLoaded('breed')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'weight' => $this->weight,
            'address' => $this->address,
            'profile_image' => $this->profile_image,
            'cover_image' => $this->cover_image,
            'gender' => $this->gender,
            'size' => $this->size,
            'desc' =>  $this->desc,
            'health_status' => $this->health_status,
            'birth_date' => $this->birth_date,   
            'status'  => $this->status,
        ];
    }
}
