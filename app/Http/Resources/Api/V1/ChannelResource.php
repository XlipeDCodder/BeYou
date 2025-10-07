<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ChannelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'avatar_url' => $this->avatar_image_path
                            ? Storage::disk('public')->url($this->avatar_image_path)
                            : null, 
            'cover_url' => $this->cover_image_path
                            ? Storage::disk('public')->url($this->cover_image_path)
                            : null, 
            'verified' => $this->verified_at !== null,

            
            'videos' => VideoResource::collection($this->whenLoaded('videos')),
        ];
    }
}