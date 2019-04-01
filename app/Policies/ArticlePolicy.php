<?php

namespace App\Policies;

use App\User;
use App\Article;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ArticlePolicy
 * @package App\Policies
 */
class ArticlePolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param Article $article
     * @return bool
     *
     * @author duc <1025434218@qq.com>
     */
    public function own(User $user, Article $article)
    {
        return $article->author_id == $user->id || $user->isAdmin();
    }
}
