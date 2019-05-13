<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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
            'id'     => $this->id,
            'author' => $this->whenLoaded('author', function () {
                return [
                   'id'     => $this->author->id,
                   'name'   => $this->author->name,
                   'avatar' => $this->author->avatar,
                ];
            }),
            'headImage'         => $this->head_image,
            'category'          => array_random(['php', 'Linux', 'Js']),
            'content'           => $this->content_html,
            'content_md'        => $this->content_md,
            'title'             => $this->title,
            'desc'              => $this->desc,
            'tags'              => $this->whenLoaded('tags', TagResource::collection($this->tags)),
            'comments'          => $this->whenLoaded('comments', CommentResource::collection($this->comments)),
            'recommendArticles' => $this->when($this->recommendArticles, $this->recommendArticles),
            'created_at'        => $this->created_at->diffForHumans(),
            'updated_at'        => $this->updated_at->diffForHumans(),
        ];
    }
}
