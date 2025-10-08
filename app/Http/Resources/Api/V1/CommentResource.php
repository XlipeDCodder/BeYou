<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'body' => $this->body,
            'created_at' => $this->created_at->diffForHumans(), 
            'author' => [
                'name' => $this->user->name,
            ],
            'can_delete' => ($user = auth('api')->user()) && $user->can('delete', $this->resource),
        ];
    }
}