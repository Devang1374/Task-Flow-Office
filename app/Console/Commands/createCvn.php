<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\createCsv;

use Illuminate\Contracts\Console\PromptsForMissingInput;

class createCvn extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:create {userId}';

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
        $userId = $this->argument('userId');

        createCsv::dispatch($userId);

        $this->info("Csv File is created in public storage folder");
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'userId' => 'Provide user Id to create There task CSV file',
        ];
    }
}
