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
            'nickname' => $this->nickname,
            'updated_at' => $this->updated_at,
            'matches' => $this->whenLoaded('matches', Match::collection($this->matches)),
        ];
    }
}
