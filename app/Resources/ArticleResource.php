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
            'id' => $this->id,
            'author' => $this->whenLoaded('author', function () {
                return [
                   'id' => $this->author->id,
                   'name' => $this->author->name,
                   'avatar' => $this->author->avatar,
                ];
            }),
            'headImage' => $this->head_image,
            'category' => array_random(['php', 'Linux', 'Js']),
            'content' => $this->content,
            'title' => $this->title,
            'desc' => $this->desc,
            'tags' => [
              ["id"=> 1, "name"=> "php" ],
              ["id"=> 2, "name"=> "linux" ],
              ["id"=> 3, "name"=> "max" ]
            ],
            'comments' => [
                [
                    "id" => 1,
                    "created_at" => "2018-10-1",
                    "author" => [
                        "id" => 1,
                        "name" => "duc",
                        "avatar" => "images/comment_author_1.jpg"
                    ],
                    "body" => "这里是评论的主题"
                ],[
                    "id" => 2,
                    "created_at" => "2018-10-1",
                    "author" => [
                        "id" => 1,
                        "name" => "duc",
                        "avatar" => "images/comment_author_1.jpg"
                    ],
                    "body" => "这里是评论的主题"
                ],[
                    "id" => 3,
                    "created_at" => "2018-10-1",
                    "author" => [
                        "id" => 1,
                        "name" => "duc",
                        "avatar" => "images/comment_author_1.jpg"
                    ],
                    "body" => "这里是评论的主题"
                ],
            ],
            'recommendArticles' => $this->when($this->recommendArticles, $this->recommendArticles),
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->diffForHumans(),
        ];
    }
}
