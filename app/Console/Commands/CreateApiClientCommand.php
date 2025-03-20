<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ApiClient;
use Illuminate\Support\Facades\Hash;

class CreateApiClientCommand extends Command
{
    protected $signature = 'api-client:create {name} {secret} {--admin}';
    protected $description = 'Create new api client';

    public function handle()
    {
        $name = $this->argument('name');
        $secret = $this->argument('secret');
        $isAdmin = $this->option('admin');

        $client = ApiClient::create([
            'name' => $name,
            'token_version' => 0,
            'client_secret' => Hash::make($secret),
            'is_admin' => $isAdmin,
        ]);

        $this->info("API-клиент создан: ID={$client->id}, Name={$client->name}");
    }
}
