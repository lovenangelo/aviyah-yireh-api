<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Events;

class DeleteAllEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:delete-all-events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = Events::count();
        Events::truncate();

        $this->info("Deleted ALL events. Total deleted: {$count}");
    }
}
