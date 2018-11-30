<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'access'     => ['super_admin', 'admin'],
            'token'      => 'super_admin',
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'mobile'     => $this->mobile,
            'avatar'     => $this->avatar,
            'bio'        => $this->bio,
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->diffForHumans(),
        ];
    }
}
