<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
           'id'         => $this->id,
           'body'       => $this->content,
           'created_at' => $this->created_at->diffForHumans(),
            'author'    => [
                'id'     => 1,
                'name'   => 'duc',
                'avatar' => 'images/comment_author_1.jpg',
            ],
        ];
    }
}
