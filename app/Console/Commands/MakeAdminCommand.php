<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-admin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make admin command';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument("email");

        $user = User::where("email", $email)->first();
        if(!$user){
            $this->error("User with email '$email' not found");
            return;
        }

        if ($user->is_admin){
            $this->warn("User is already admin");
            return;
        }

        $user->is_admin = true;
        $user->save();

        $this->info("DONE!");


    }
}
