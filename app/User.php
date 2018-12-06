<?php

namespace App;

use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'name', 'email', 'avatar', 'mobile', 'bio', 'password',
     ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($user) {
            $user->articles->each->delete();
            $user->categories->each->update(['user_id' => 0]);
            $user->tags->each->update(['user_id' => 0]);
            $user->articleRules->each->delete();
        });
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function articleRules()
    {
        return $this->hasMany(ArticleRegular::class, 'user_id');
    }

    public function activeArticleRules()
    {
        return $this->articleRules()->where('status', true);
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function isAdmin()
    {
        return $this->id === 1;
    }

    public function getAvatarAttribute($value)
    {
        return is_null($value) ? URL::asset('blog/default-avatar.png') : $value;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
