<?php

namespace App\Console\Commands;

use App\Jobs\AnalyzeMatch;
use App\Models\Match;
use App\Riot\MatchAnalyzer;
use Illuminate\Console\Command;

class TestMatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:match';

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
    }
}
