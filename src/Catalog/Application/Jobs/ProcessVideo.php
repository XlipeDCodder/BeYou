<?php

namespace Beyou\Catalog\Application\Jobs;

use Beyou\Catalog\Domain\Enums\VideoStatus;
use Beyou\Catalog\Domain\Model\Video;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use FFMpeg\Coordinate\TimeCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Video $video)
    {
    }

    public function handle(): void
    {
        Log::info("Iniciando processamento do vídeo #{$this->video->id} - {$this->video->title}");

        try {
            $originalPath = $this->video->original_file_path;

            
            $thumbnailFilename = $this->video->uuid . '.jpg';
            $streamFilename = $this->video->uuid . '.mp4';

            
            $thumbnailRelativePath = 'uploads/videos/thumbnails/' . $thumbnailFilename;
            $streamRelativePath = 'uploads/videos/streams/' . $streamFilename;

            $thumbnailFullPath = Storage::disk('public')->path($thumbnailRelativePath);
            $streamFullPath = Storage::disk('public')->path($streamRelativePath);

            
            Storage::disk('public')->makeDirectory('uploads/videos/thumbnails');
            Storage::disk('public')->makeDirectory('uploads/videos/streams');

            Log::info("Caminhos de destino definidos", [
                'thumbnail' => $thumbnailFullPath,
                'stream' => $streamFullPath,
            ]);

            
            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => 'C:/ffmpeg/bin/ffmpeg.exe',
                'ffprobe.binaries' => 'C:/ffmpeg/bin/ffprobe.exe',
                'timeout'          => 3600,
                'ffmpeg.threads'   => 12,
            ]);

            
            $videoFile = $ffmpeg->open(Storage::disk('local')->path($originalPath));

            
            Log::info("Gerando thumbnail...");
            $videoFile->frame(TimeCode::fromSeconds(1))
                ->save($thumbnailFullPath);
            Log::info("Thumbnail gerado com sucesso", ['path' => $thumbnailFullPath]);

            
            Log::info("Extraindo duração do vídeo...");
            $duration = $videoFile->getStreams()
                ->videos()
                ->first()
                ->get('duration');
            $duration = round($duration ?? 0);
            Log::info("Duração extraída", ['duration_in_seconds' => $duration]);

            
            Log::info("Iniciando transcodificação...");
            $format = new X264('aac', 'libx264');
            $format->setAdditionalParameters(['-crf', '18', '-preset', 'slow']);

            $videoFile->save($format, $streamFullPath);
            Log::info("Transcodificação concluída", ['path' => $streamFullPath]);

            
            $this->video->update([
                'status' => VideoStatus::PUBLISHED,
                'thumbnail_path' => $thumbnailRelativePath,
                'stream_file_path' => $streamRelativePath,
                'duration_in_seconds' => $duration,
                'published_at' => now(),
            ]);

            
            Storage::disk('local')->delete($originalPath);

            Log::info("Vídeo #{$this->video->id} processado com sucesso!");

        } catch (\Throwable $e) {
            $this->video->update(['status' => VideoStatus::FAILED]);

            Log::error("Falha ao processar vídeo #{$this->video->id}: {$e->getMessage()}", [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
