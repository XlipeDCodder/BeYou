<?php

namespace Beyou\Catalog\Domain\Model;

use Beyou\Catalog\Domain\Enums\VideoStatus;
use Beyou\Catalog\Domain\Enums\VideoVisibility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Video extends Model
{
    use HasFactory, HasUuids;

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true; // <-- ADICIONE ESTA LINHA

    /**
     * Get the data type for the model's primary key.
     *
     * @return string
     */
    public function getKeyType(): string 
    {
        return 'int';
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array
     */
    public function uniqueIds(): array 
    {
        return ['uuid'];
    }

    /**
     * Os atributos que podem ser atribuídos em massa.
     */
    protected $fillable = [
        'channel_id',
        'title',
        'description',
        'original_file_path',
        'stream_file_path',
        'thumbnail_path',
        'duration_in_seconds',
        'status',
        'visibility',
        'published_at',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     */
    protected $casts = [
        'published_at' => 'datetime',
        'status' => VideoStatus::class, 
        'visibility' => VideoVisibility::class,
    ];

    /**
     * Obtém o canal ao qual o vídeo pertence.
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    
    public function reactions(): HasMany
    {
        return $this->hasMany(\Beyou\Engagement\Domain\Model\VideoReaction::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(\Beyou\Engagement\Domain\Model\Comment::class);
    }
}