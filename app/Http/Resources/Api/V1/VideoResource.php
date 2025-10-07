<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description, 
            'stream_url' => $this->stream_file_path 
                            ? Storage::disk('public')->url($this->stream_file_path)
                            : null,
            'thumbnail_url' => $this->thumbnail_path
                                ? Storage::disk('public')->url($this->thumbnail_path)
                                : null,
            'duration_in_seconds' => $this->duration_in_seconds,
            'published_at' => $this->published_at,
            'channel' => [
                'name' => $this->channel->name,
                'slug' => $this->channel->slug,
            ],
        ];
    }
}