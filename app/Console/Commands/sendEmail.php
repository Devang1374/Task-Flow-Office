<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use App\Mail\tampMail;
use Illuminate\Support\Facades\Mail;
use function Laravel\Prompts\search;

use App\Models\User;

class sendEmail extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send {user} {--Q|queue=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Email to User';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //Mail::to("{$this->argument('user')}123@gmail.com")->queue(new tampMail($this->argument('user')));
        $city = $this->choice('What is Your City Name', ['Bhavnagar', 'Rajkot'], 1, 3, true);
     
        $user = $this->argument('user');
        if($this->confirm("Do Your Relly want to Send mail to {$user}", true)){
            
            foreach($city as $c)
                $this->info("Sending mail to {$user} in $c");

            $users[] = $this->withProgressBar(User::all(), function (User $use) {
                    return $use['name'];
            });

            $this->table(['city', 'city'],[$city, $city]);
        }else{
            $this->info("Mail Cancel");
        }
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'user' => fn () => search(
                label: "Which User should Receive the mail?",
                placeholder: 'E.g. Devang',
                options: fn () => ['Devang','Raj','Jay']
            ), 
        ];
    }
}
