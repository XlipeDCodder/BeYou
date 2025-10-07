<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\VideoResource;
use Beyou\Catalog\Domain\Enums\VideoStatus;
use Beyou\Catalog\Domain\Model\Video;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SearchController extends Controller
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        
        $request->validate([
            'q' => ['required', 'string', 'min:3'],
        ]);

        $searchTerm = $request->input('q');

        
        $videos = Video::query()
            
            ->with('channel')
            ->withCount([ // <-- ADICIONE ESTE BLOCO
                'reactions as likes_count' => fn ($q) => $q->where('type', 'like'),
                'reactions as dislikes_count' => fn ($q) => $q->where('type', 'dislike'),
            ])
            
            ->where('status', VideoStatus::PUBLISHED)
            
            ->where(function ($query) use ($searchTerm) {
                $query->where('title', 'ilike', "%{$searchTerm}%")
                      ->orWhere('description', 'ilike', "%{$searchTerm}%");
            })
            
            ->latest('published_at')
            
            ->paginate(12);

        
        return VideoResource::collection($videos);
    }
}