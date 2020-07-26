<?php

namespace App\Features;

use Carbon\Carbon;
use App\Models\User;
use App\Riot\Valorant;
use App\Riot\PlayerAnalyzer;
use GuzzleHttp\Client;

trait PlayerProcessing
{
    use ServiceSettings;

    /**
     * @return mixed
     */
    public function analyzeMatches()
    {
        $changed = Carbon::createFromTimestamp((int) $this->getSetting('valorant.match_analyzer_changed', 0))
            ->toDateTimeString();

        $unprocessedMatches = $this->matches()
            ->select('id', 'processed_at', 'original')
            ->whereNotNull('original')
            ->where(function ($q) use ($changed) {
                $q->whereNull('processed_at')
                    ->orWhere('processed_at', '<', $changed);
            })
            ->get();

        foreach ($unprocessedMatches as $match)
        {
            try {
                $match->analyze();
            } catch (\Exception $e) {
                app('log')->error('Cant analyze match #' . $match->id . ': ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            }
        }
    }

    /**
     * @return void
     */
    public function analyze()
    {
        $this->analyzeMatches();

        $changed = Carbon::createFromTimestamp((int) $this->getSetting('valorant.match_analyzer_changed', 0))
            ->toDateTimeString();

        $processedMatches = $this->matches()
            ->where('is_custom', false)
            ->whereNotNull('processed_at')
            ->orderBy('started_at', 'desc')
            ->select('id', 'stats', 'processed_at')
            ->where('processed_at', '>', $changed)
            ->get();

        $analyzer = new PlayerAnalyzer($this, $processedMatches->toArray());

        $this->stats = $analyzer->toArray();
        $this->processed_at = Carbon::now();
        $this->save();
    }

    /**
     * @param $nickname
     * @param $tag
     *
     * @return User|null
     */
    public function findExact($nickname, $tag)
    {
        $tag = mb_strtolower($tag);
        $nickname = mb_strtolower($nickname);

        $data = (string) (new Client)->get("https://valorant.iesdev.com/player/{$nickname}-{$tag}")->getBody();

        $player = json_decode($data);

        if (! isset($player->id)) {
            return null;
        }

        $user = User::firstOrCreate(['uid' => $player->id]);

        $user->tag = $player->tag;
        $user->nickname = $player->name;
        $user->save();

        return $user;
    }

    /**
     * @param $nickname
     * @param $tag
     *
     * @return User|null
     */
    public function findExactOld($nickname, $tag)
    {
        /**
         * @var Valorant $api
         */
        $api = app(Valorant::class);

        $uid = $api->getUid($nickname, $tag);

        if (! isset($uid->uid)) {
            return null;
        }

        $user = User::firstOrCreate(['uid' => $uid->uid]);

        $user->tag = $tag;
        $user->nickname = $nickname;
        $user->save();

        return $user;
    }
}
