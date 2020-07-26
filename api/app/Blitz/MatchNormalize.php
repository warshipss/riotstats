<?php

namespace App\Blitz;

class MatchNormalize
{
    /**
     * @param $data
     * @return array
     */
    public static function handle($data)
    {
        return $data;
        return [
            'teams' => collect($data->teams)->keyBy('teamId')->toArray(),
            'players' => collect($data->players)->keyBy('subject')->map(function ($player) {
                return array_merge([
                    'team' => $player->teamId,
                    'party' => $player->partyId,
                    'agent' => $player->characterId,
                ], (array) $player->stats);
            })->toArray(),
        ];
    }
}
