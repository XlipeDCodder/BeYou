<?php

namespace Beyou\Catalog\Application;

use App\Models\User;
use Beyou\Catalog\Domain\Model\Channel;
use Illuminate\Support\Str;

class CreateChannelService
{
    public function execute(User $user, string $name): Channel
    {
        
        if ($user->channel()->exists()) {
            
            throw new \Exception('O usuário já possui um canal.');
        }

        
        $channelData = [
            'name' => $name,
            'slug' => Str::slug($name), 
        ];

        
        $channel = $user->channel()->create($channelData);

        return $channel;
    }
}