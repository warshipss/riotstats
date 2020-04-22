<?php

namespace App\Features;

use Carbon\Carbon;
use App\Riot\PlayerAnalyzer;

trait PlayerProcessing
{
    use ServiceSettings;

    /**
     * @return mixed
     */
    public function getUnprocessedMatches()
    {
        $changed = Carbon::createFromTimestamp((int) $this->getSetting('valorant.match_analyzer_changed', 0))
            ->toDateTimeString();

        $unprocessedMatches = $this->matches()
            ->withoutGlobalScopes()
            ->select('id', 'processed_at')
            ->where(function ($q) use ($changed) {
                $q->whereNull('processed_at')
                    ->orWhere('processed_at', '<', $changed);
            })
            ->get();

        return $unprocessedMatches;
    }

    /**
     * @return void
     */
    public function process()
    {
        $changed = Carbon::createFromTimestamp((int) $this->getSetting('valorant.match_analyzer_changed', 0))
            ->toDateTimeString();

        $processedMatches = $this->matches()
            ->withoutGlobalScopes()
            ->whereNotNull('processed_at')
            ->orderBy('started_at', 'desc')
            ->where('stats->type', 'matchmaking')
            ->select('id', 'stats', 'processed_at')
            ->where('processed_at', '>', $changed)
            ->get();

        $analyzer = new PlayerAnalyzer($this, $processedMatches->toArray());

        $this->stats = $analyzer->toArray();
        $this->processed_at = Carbon::now();
        $this->save();
    }
}
