<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Match extends JsonResource
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
            'data' => $this->data,
            'original' => $this->original,
            'users' => ShortProfile::collection($this->users),
        ];
    }
}
