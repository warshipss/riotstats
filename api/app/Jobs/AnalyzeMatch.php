<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Match;
use App\Riot\Valorant;
use App\Riot\TokenFeatures;
use App\Riot\MatchAnalyzer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class AnalyzeMatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $analyzer = new MatchAnalyzer($this->match->original);

        $this->match->data = $analyzer->toArray();
        $this->match->processed_at = Carbon::now();
        $this->match->save();
    }
}
