<?php

namespace App\Console\Commands;

use App\Models\Match;
use App\Models\Token;
use App\Riot\Valorant;
use App\Riot\TokenFeatures;
use Illuminate\Console\Command;
use App\Exceptions\InvalidTokenException;

class MoveDatabase extends Command
{
    use TokenFeatures;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'move:database {start}';

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
     * @return void
     *
     * @throws \App\Exceptions\InvalidTokenException
     */
    public function handle()
    {
        $start = (int) $this->argument('start');

        $total = Match::on('mysql')->count();
        $iterations = ceil(($total - $start) / 1e3);

        for ($i = 0; $i < $iterations; $i++)
        {
            $matches = Match::on('mysql')
                ->withoutGlobalScopes()
                ->limit(1e3)
                ->offset($start + ($i * 1e3))
                ->get();

            foreach ($matches as $match) {
                Match::on('pgsql')->insert($match->toArray());
            }

            $this->info('Iteration #' . $i . ' completed');
        }
    }
}
