<?php

namespace App\Policies;

use App\User;
use App\Comment;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * @param  User  $user
     * @param  Comment  $comment
     * @return bool
     *
     * @author duc <1025434218@qq.com>
     */
    public function own(User $user, Comment $comment)
    {
        return $comment->article->author_id == $user->id || $user->isAdmin();
    }
}
