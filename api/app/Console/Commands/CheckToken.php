<?php

namespace App\Console\Commands;

use App\Riot\TokenFeatures;
use App\Jobs\ObtainNewToken;
use Illuminate\Console\Command;

class CheckToken extends Command
{
    use TokenFeatures;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:token';

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
        $token = $this->getToken();

        if (! $token) {
            ObtainNewToken::dispatch();
        }
    }
}
