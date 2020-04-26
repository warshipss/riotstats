<?php

namespace App\Riot;

use App\Models\User;

class PlayerAnalyzer
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var array
     */
    protected $matches;

    /**
     * MatchAnalyzer constructor.
     *
     * @param User $user
     * @param array $matches
     */
    public function __construct($user, $matches)
    {
        $this->user = $user;
        $this->matches = $matches;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge($this->getSummary(), [
            'record' => $this->getRecord(),
            'matches' => count($this->matches),
        ]);
    }

    /**
     * @return object
     */
    protected function getRecord()
    {
        $uid = $this->user->uid;
        $matches = $this->matches;

        $record = (object) [
            'kda' => [],
            'kills' => 0,
            'kda_sum' => 0,
        ];

        for ($i = 0; $i < count($matches) -1; $i++)
        {
            $match = $matches[$i];
            $player = $match['stats']->players->{$uid};

            $kda_sum = ($player->kills + ($player->assists - $player->deaths) * 0.5);

            if ($kda_sum > $record->kda_sum)
            {
                $record->kda_sum = $kda_sum;
                $record->kda = [
                    'kills' => $player->kills,
                    'deaths' => $player->deaths,
                    'assists' => $player->assists,
                ];
            }
        }

        return $record;
    }

    /**
     * @return int[]
     */
    protected function getSummary()
    {
        $stats = [
            'wins' => 0,
            'score' => 0,
            'kills' => 0,
            'deaths' => 0,
            'rounds' => 0,
            'damage' => 0,
            'matches' => 0,
            'assists' => 0,
        ];

        $uid = $this->user->uid;
        $matches = $this->matches;

        $byAgent = [];
        $summary = $last20 = $stats;

        for ($i = 0; $i < count($matches) -1; $i++)
        {
            $match = $matches[$i];
            $player = $match['stats']->players->{$uid};

            if ($i < 20) {
                $last20 = $this->summarize($last20, $player, $match['stats']);
            }

            $summary = $this->summarize($summary, $player, $match['stats']);

            if (! isset($byAgent[$player->agent])) {
                $byAgent[$player->agent] = $stats;
            }

            $byAgent[$player->agent] = $this->summarize($byAgent[$player->agent], $player, $match['stats']);
        }

        return compact('summary', 'last20', 'byAgent');
    }

    /**
     * @param $array
     * @param $player
     * @param $stats
     *
     * @return mixed
     */
    protected function summarize($array, $player, $stats)
    {
        foreach ($array as $k => $v)
        {
            if ($k === 'matches')
            {
                $array[$k]++;
                continue;
            }

            if ($k === 'wins')
            {
                if ($this->getWinnerTeam($stats) === $player->team) {
                    $array[$k]++;
                }

                continue;
            }

            $array[$k] += $player->{$k};
        }

        return $array;
    }

    /**
     * @param $stats
     *
     * @return int|string
     */
    protected function getWinnerTeam($stats)
    {
        foreach ((array) $stats->teams as $key => $data)
        {
            if ($data->won === true) {
                return $key;
            }
        }

        return 0;
    }
}
