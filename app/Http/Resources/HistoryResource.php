<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'ip'          => $this->ip,
            'url'         => $this->url,
            'content'     => $this->content,
            'method'      => $this->method,
            'status_code' => (int) $this->status_code,
            'address'     => $this->address,
            'response'    => $this->response,
            'user_agent'  => $this->user_agent,
            'visited_at'  => $this->visited_at,
            'user'        => $this->when($this->resource->relationLoaded('userable'), function () {
                return [
                    'id'   => optional($this->userable)->id ?? 0,
                    'name' => optional($this->userable)->name ?? '',
                ];
            }, [
                'id'   => 0,
                'name' => '',
            ]),
        ];
    }
}
