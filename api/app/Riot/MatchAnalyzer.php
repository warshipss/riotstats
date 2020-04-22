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
        $this->original = json_decode($original);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $players = $this->getPlayers();

        return [
            'players' => $players,
            'teams' => $this->getTeams(),
            'versus' => $this->getVersus(),
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

        foreach ($this->original->kills as $kill)
        {
            if (! isset($versus[$kill->killer])) {
                $versus[$kill->killer] = [];
            }

            if (! isset($versus[$kill->killer][$kill->victim])) {
                $versus[$kill->killer][$kill->victim] = 0;
            }

            $versus[$kill->killer][$kill->victim]++;
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

        foreach ($this->original->players as $player) {
            $players[$player->subject] = $this->getPlayer($player);
        }

        return $players;
    }

    /**
     * @param $original
     *
     * @return array
     */
    protected function getPlayer($original)
    {
        $damage = $this->getTotalDamage($original);

        return [
            'damage' => $damage,
            'team' => $original->teamId,
            'party' => $original->partyId,
            'agent' => $original->characterId,
            'score' => $original->stats->score,
            'kills' => $original->stats->kills,
            'deaths' => $original->stats->deaths,
            'casts' => $this->getCasts($original),
            'assists' => $original->stats->assists,
            'rounds' => $original->stats->roundsPlayed,
            'weapons' => $this->getByWeapon($original->subject),
            'adr' => floor($damage / $original->stats->roundsPlayed),
        ];
    }

    /**
     * @param $killer
     *
     * @return array
     */
    protected function getByWeapon($killer)
    {
        $weapons = [];

        foreach ($this->original->kills as $kill)
        {
            if ($kill->killer !== $killer) {
                continue;
            }

            $item = mb_strtolower($kill->finishingDamage->damageItem);

            if (! isset($weapons[$item])) {
                $weapons[$item] = 0;
            }

            $weapons[$item]++;
        }

        return $weapons;
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
