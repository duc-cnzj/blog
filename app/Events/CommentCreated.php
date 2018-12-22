<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class CommentCreated extends Event implements ShouldBroadcastNow
{
    use SerializesModels, InteractsWithSockets;

    public $comment;

    public function __construct($comment)
    {
        info('CommentCreated', $comment->toArray());
        $this->comment = $comment;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\Channel[]
     */
    public function broadcastOn()
    {
        return new Channel('article.' . $this->comment->article->id . '.comments');
    }

    public function broadcastWith()
    {
        return [
            'id'         => $this->comment->id,
            'body'       => $this->comment->content,
            'comment_id' => $this->comment->comment_id,
            'created_at' => optional($this->comment->created_at)->diffForHumans(),
            'author'     => $this->getAuthor(),
            'replies'    => [],
        ];
    }

    private function getAuthor()
    {
        return is_null($this->comment->userable)
            ? [
                'name'   => $this->comment->visitor,
                'avatar' => '',
            ]
            : [
                'name'   => $this->comment->userable->name,
                'avatar' => $this->comment->userable->avatar,
            ];
    }
}
