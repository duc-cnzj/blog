<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class UserPolicy
 * @package App\Policies
 */
class UserPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $authUser
     * @param User $modifyUser
     * @return bool
     *
     * @author duc <1025434218@qq.com>
     */
    public function own(User $authUser, User $modifyUser)
    {
        return $authUser->isAdmin() || $modifyUser->id == $authUser->id;
    }
}
