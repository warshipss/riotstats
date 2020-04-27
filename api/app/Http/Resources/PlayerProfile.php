<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlayerProfile extends JsonResource
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
            'uid' => $this->uid,
            'tag' => $this->tag,
            'stats' => $this->stats,
            'nickname' => $this->nickname,
            'updated_at' => $this->processed_at,
            'matches' => Match::collection($this->whenLoaded('matches')),
        ];
    }
}
