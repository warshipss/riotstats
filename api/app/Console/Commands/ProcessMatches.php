<?php

namespace App\Console\Commands;

use App\Jobs\AnalyzeMatch;
use App\Models\Match;
use Illuminate\Console\Command;

class ProcessMatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:matches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $matches = Match::whereNull('processed_at')
            ->get();

        foreach ($matches as $match) {
            AnalyzeMatch::dispatch($match);
        }
    }
}
