<?php

namespace App\Events;

use App\Article;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ArticleCreated extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $article;

    /**
     * 创建一个新的事件实例。
     *
     * @param $article
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     *获得事件广播的频道。
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('article.created');
    }

    public function broadcastWith()
    {
        return [
            'id'      => $this->article->id,
            'title'   => $this->article->title,
            'desc'    => $this->article->desc,
            'author'  => $this->article->author->name,
        ];
    }
}
