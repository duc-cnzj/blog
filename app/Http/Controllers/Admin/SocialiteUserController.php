<?php

namespace App\Http\Controllers\Admin;

use App\SocialiteUser;
use Illuminate\Http\Request;
use App\Filters\SocialiteUserFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\SocialiteUserResource;

/**
 * Class SocialiteUserController
 * @package App\Http\Controllers\Admin
 */
class SocialiteUserController extends Controller
{
    /**
     * @param  Request  $request
     * @param  SocialiteUserFilter  $filter
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @author duc <1025434218@qq.com>
     */
    public function index(Request $request, SocialiteUserFilter $filter)
    {
        $users = SocialiteUser::filter($filter)
            ->latest()
            ->paginate(
                $request->input('page_size') ?? 10,
                ['id', 'name', 'avatar', 'identity_type', 'last_login_at', 'created_at']
            );

        return SocialiteUserResource::collection($users);
    }
}
