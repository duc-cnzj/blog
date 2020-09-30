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
            'comment_id' => $this->comment_id,
            'created_at' => $this->created_at->diffForHumans(),
            'author'     => $this->when(
                ! is_null($this->userable),
                function () {
                    $data = [
                    'name'   => $this->userable->name,
                    'avatar' => $this->userable->avatar,
                ];

                    return $data;
                },
                [
                'name'   => $this->visitor,
                'avatar' => '',
             ]
            ),
            'article' => $this->whenLoaded('article', function () {
                return new ArticleResource($this->article);
            }),
        ];
    }
}
