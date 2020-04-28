<?php

namespace App\Riot;

class MatchAnalyzer
{
    /**
     * @var \stdClass
     */
    protected $original;

    /**
     * MatchAnalyzer constructor.
     *
     * @param $original
     */
    public function __construct($original)
    {
        $this->original = $original;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'teams' => $this->getTeams(),
            'versus' => $this->getVersus(),
            'players' => $this->getPlayers(),
            'map' => $this->original->matchInfo->mapId,
            'ranked' => $this->original->matchInfo->isRanked,
            'completed' => $this->original->matchInfo->isCompleted,
            'type' => $this->original->matchInfo->provisioningFlowID === 'Matchmaking' ? 'matchmaking' : 'custom',
        ];
    }

    /**
     * @return array
     */
    protected function getVersus()
    {
        $versus = [];
        $uids = collect($this->original->players)
            ->mapWithKeys(function ($player) {
                return [$player->subject => 0];
            });

        foreach ($uids as $uid => $arr) {
            $versus[$uid] = $uids->toArray();
        }

        if (! $this->original->kills) {
            return $versus;
        }

        foreach ($this->original->kills as $kill)
        {
            if ($kill->killer && $kill->victim) {
                $versus[$kill->killer][$kill->victim]++;
            }
        }

        return $versus;
    }

    /**
     * @return \stdClass
     */
    protected function getTeams()
    {
        $result = new \stdClass;

        foreach ($this->original->teams as $team) {
            $result->{$team->teamId} = $team;
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getPlayers(): array
    {
        $players = [];

        foreach ($this->original->players as $player)
        {
            $damage = $this->getTotalDamage($player);

            $players[$player->subject] = [
                'entries' => [
                    'kills' => 0,
                    'deaths' => 0,
                ],
                'multi' => [
                    '3k' => 0,
                    '4k' => 0,
                    '5k' => 0,
                ],
                'weapons' => [],
                'damage' => $damage,
                'team' => $player->teamId,
                'party' => $player->partyId,
                'agent' => $player->characterId,
                'casts' => $this->getCasts($player),
                'score' => $player->stats->score || 0,
                'kills' => $player->stats->kills || 0,
                'deaths' => $player->stats->deaths || 0,
                'assists' => $player->stats->assists || 0,
                'rounds' => $player->stats->roundsPlayed || 0,
            ];
        }

        if (! $this->original->kills) {
            return $players;
        }

        $byRound = collect($this->original->kills)
            ->groupBy('round');

        foreach ($byRound as $round => $kills)
        {
            $kills = $kills->sortBy('roundTime');
            $first = $kills->first();

            $players[$first->killer]['entries']['kills']++;
            $players[$first->victim]['entries']['deaths']++;

            $grouped = $kills->groupBy('killer');

            foreach ($grouped as $killer => $kills)
            {
                $count = $kills->count();

                foreach ($kills as $kill)
                {
                    $weapon = mb_strtolower($kill->finishingDamage->damageItem);

                    if ($weapon)
                    {
                        if (! isset($players[$kill->killer]['weapons'][$weapon])) {
                            $players[$kill->killer]['weapons'][$weapon] = 0;
                        }

                        $players[$kill->killer]['weapons'][$weapon]++;
                    }
                }

                if ($count >= 3 && $count <= 5) {
                    $players[$killer]['multi'][$count . 'k']++;
                }
            }
        }

        return $players;
    }

    /**
     * @param $original
     *
     * @return mixed
     */
    protected function getCasts($original)
    {
        if (! isset($original->stats->abilityCasts))
        {
            return [
                'grenade' => 0,
                'ability1' => 0,
                'ability2' => 0,
                'ultimate' => 0,
            ];
        }

        $result = [];
        $casts = (array) $original->stats->abilityCasts;

        foreach ($casts as $key => $value) {
            $result[str_replace('Casts', '', $key)] = (int) $value;
        }

        return $result;
    }

    /**
     * @param $original
     *
     * @return float|int
     */
    protected function getTotalDamage($original)
    {
        if ($original->roundDamage === null) {
            return 0;
        }

        return array_sum(array_map(function ($dmg) {
            return $dmg->damage === 999 ? 0 : $dmg->damage;
        }, $original->roundDamage));
    }
}
