<?php

namespace App\Riot;

class MatchAnalyzer
{
    /**
     * @var \stdClass
     */
    protected $original;

    /**
     * @var string[]
     */
    protected $maps = [
        '/Game/Maps/Triad/Triad' => 'haven',
        '/Game/Maps/Bonsai/Bonsai' => 'split',
        '/Game/Maps/Duality/Duality' => 'bind',
    ];

    /**
     * @var string[]
     */
    protected $agents = [
        '5f8d3a7f-467b-97f3-062c-13acf203c006' => 'breach',
        'f94c3b30-42be-e959-889c-5aa313dba261' => 'raze',
        '117ed9e3-49f3-6512-3ccf-0cada7e3823b' => 'cypher',
        '320b2a48-4d9b-a075-30f1-1f93a9b638fa' => 'sova',
        '707eab51-4836-f488-046a-cda6bf494859' => 'viper',
        'eb93336a-449b-9c1b-0a54-a891f7921d69' => 'phoenix',
        '9f0d8ba9-4140-b941-57d3-a7ad57c6b417' => 'brimstone',
        '569fdd95-4d10-43ab-ca70-79becc718b46' => 'sage',
        '8e253930-4c05-31dd-1b6c-968525494517' => 'omen',
        'add6443a-41bd-e414-f6ad-e58d267f4e95' => 'jett',
    ];

    /**
     * MatchAnalyzer constructor.
     *
     * @param $original
     */
    public function __construct($original)
    {
        $this->original = json_decode($original);
    }

    public function toArray()
    {
        return [
            'map' => $this->getMap(),
            'score' => $this->getScore(),
            'players' => $this->getPlayers(),
        ];
    }

    /**
     * @return mixed|string
     */
    protected function getMap()
    {
        $map = $this->original->matchInfo->mapId;

        if (isset($this->maps[$map])) {
            return $this->maps[$map];
        }

        $split = explode('/', $map);

        return $split[count($split) - 1];
    }

    /**
     * @return \stdClass
     */
    protected function getScore()
    {
        $teams = new \stdClass;

        foreach ($this->original->roundResults as $round)
        {
            $winner = $round->winningTeam;

            if (isset($teams->{$winner})) {
                $teams->{$winner}++;
            } else {
                $teams->{$winner} = 1;
            }
        }

        return $teams;
    }

    /**
     * @return \stdClass
     */
    protected function getPlayers()
    {
        $players = new \stdClass;

        foreach ($this->original->players as $player) {
            $players->{$player->subject} = $this->getPlayer($player);
        }

        return $players;
    }

    /**
     * @param $original
     * @return array
     */
    protected function getPlayer($original)
    {
        $damage = $this->getTotalDamage($original);

        return [
            'damage' => $damage,
            'team' => $original->teamId,
            'party' => $original->partyId,
            'score' => $original->stats->score,
            'kills' => $original->stats->kills,
            'deaths' => $original->stats->deaths,
            'agent' => $this->getAgent($original),
            'assists' => $original->stats->assists,
            'rounds' => $original->stats->roundsPlayed,
            'adr' => floor($damage / $original->stats->roundsPlayed),
        ];
    }

    /**
     * @param $player
     *
     * @return mixed|string
     */
    protected function getAgent($player)
    {
        $agent = $player->characterId;

        if (isset($this->agents[$agent])) {
            return $this->agents[$agent];
        }

        return explode('-', $agent)[0];
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
