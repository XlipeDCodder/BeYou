<?php

namespace Beyou\Catalog\Domain\Model;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Channel extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'avatar_image_path',
        'cover_image_path',
        'settings',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos (casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'settings' => 'array',
    ];

    /**
     * Obtém o usuário (dono) ao qual o canal pertence.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

        public function videos(): HasMany 
    {
        return $this->hasMany(Video::class);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function subscribers(): HasMany
    {
        return $this->hasMany(\Beyou\Subscription\Domain\Model\Subscription::class);
    }
}