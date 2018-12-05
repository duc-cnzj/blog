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
            'author'     => $this->whenLoaded('user', function () {
                if (is_null($this->user->id)) {
                    $data = [
                        'name' => $this->visitor,
                        'avatar' => '',
                    ];
                } else {
                    $data = [
                        'name' => $this->user->name,
                        'avatar' => $this->user->avatar,
                    ];
                }

                return $data;
            }),
            'article' => $this->whenLoaded('article', function () {
                return new ArticleResource($this->article);
            }),
        ];
    }
}
