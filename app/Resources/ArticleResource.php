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
        if (is_array($this->resource)) {
            return $this->resource;
        }

        return [
            'id'     => $this->id,
            'author' => $this->whenLoaded('author', function () {
                return [
                   'id'     => $this->author->id,
                   'name'   => $this->author->name,
                   'avatar' => $this->author->avatar,
                ];
            }),
            'headImage' => $this->head_image,
            'category'  => $this->whenloaded('category', function () {
                return [
                    'id'   => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),
            'content'           => $this->content_html,
            'content_md'        => $this->content_md,
            'title'             => $this->title,
            'desc'              => $this->desc,
            'tags'              => TagResource::collection($this->whenLoaded('tags')),
            'comments'          => CommentResource::collection($this->whenLoaded('comments')),
            'recommendArticles' => $this->when($request->path() === 'home_articles', function () {
                return $this->getRecommendArticles();
            }),
            'created_at'        => $this->created_at->diffForHumans(),
            'highlight'         => $this->when($request->path() === 'search_articles', $this->highlight_content),
        ];
    }
}
