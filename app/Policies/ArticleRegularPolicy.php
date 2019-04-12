<?php

namespace App\Policies;

use App\User;
use App\ArticleRegular;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ArticleRegularPolicy
 * @package App\Policies
 */
class ArticleRegularPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param ArticleRegular $regular
     * @return bool
     *
     * @author duc <1025434218@qq.com>
     */
    public function own(User $user, ArticleRegular $regular)
    {
        return $regular->user_id == $user->id || $user->isAdmin();
    }
}
