<?php

namespace App\Policies;

use App\Models\User;
use Beyou\Engagement\Domain\Model\Comment;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Comment $comment): bool
    {

        return $user->id === $comment->user_id;
    }


}
