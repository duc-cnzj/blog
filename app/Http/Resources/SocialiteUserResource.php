<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SocialiteUserResource extends JsonResource
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
            'id'            => $this->id,
            'name'          => $this->name,
            'avatar'        => $this->avatar,
            'identity_type' => $this->identity_type,
            'created_at'    => optional($this->created_at)->toDatetimeString(),
            'last_login_at' => optional($this->last_login_at)->diffForHumans(),
        ];
    }
}
