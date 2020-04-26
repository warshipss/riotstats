<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Match;
use App\Riot\MatchAnalyzer;
use Illuminate\Bus\Queueable;
use App\Features\ServiceSettings;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class AnalyzeMatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServiceSettings;

    /**
     * @var Match
     */
    protected $match;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Match $match)
    {
        $this->match = $match;
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return ['process', 'match:' . $this->match->id];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $changed = Carbon::createFromTimestamp($this->getSetting('valorant.match_analyzer_changed', 0));

        $analyzer = new MatchAnalyzer($this->match->original);

        $this->match->stats = $analyzer->toArray();
        $this->match->processed_at = Carbon::now();
        $this->match->save();

        $this->match->users()->touch();
    }

    /**
     * The job failed to process.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        FetchMatch::dispatch($this->match);
    }
}
