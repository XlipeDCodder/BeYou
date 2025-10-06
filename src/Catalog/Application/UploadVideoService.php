<?php

namespace Beyou\Catalog\Application;

use Beyou\Catalog\Application\Jobs\ProcessVideo;
use Beyou\Catalog\Domain\Enums\VideoStatus;
use Beyou\Catalog\Domain\Model\Channel;
use Beyou\Catalog\Domain\Model\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadVideoService
{
    public function execute(Channel $channel, array $data, UploadedFile $videoFile): Video
    {

        $originalPath = $videoFile->store('uploads/videos/originais', 'local');


        $video = $channel->videos()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'original_file_path' => $originalPath,
            'status' => VideoStatus::PROCESSING,
            'visibility' => $data['visibility'] ?? 'private', // Default para privado
        ]);


        ProcessVideo::dispatch($video);


        return $video;
    }
}