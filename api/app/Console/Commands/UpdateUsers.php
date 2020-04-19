<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Jobs\UpdatePlayer;
use App\Riot\TokenFeatures;
use Illuminate\Console\Command;
use App\Features\ServiceSettings;

class UpdateUsers extends Command
{
    use TokenFeatures, ServiceSettings;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:users';

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
        if ($this->getSetting('valorant.fetch_users') !== 'true') {
            return;
        }

        $users = User::whereNull('fetched_at')
            ->whereNull('queued_at')
            ->limit(15)
            ->get();

        foreach ($users as $user) {
            UpdatePlayer::dispatch($user);
        }
    }
}
