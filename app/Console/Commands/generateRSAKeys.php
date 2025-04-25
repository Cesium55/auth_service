<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class generateRSAKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-rsa-keys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates private and public rsa keys';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Пути к файлам
        $privateKeyPath = storage_path('keys/private.key');
        $publicKeyPath = storage_path('keys/public.key');

        // Генерация приватного ключа
        $privateKeyCommand = "openssl genpkey -algorithm RSA -out {$privateKeyPath} -pkeyopt rsa_keygen_bits:4096 > /dev/null 2>&1";
        exec($privateKeyCommand);

        // Генерация публичного ключа из приватного
        $publicKeyCommand = "openssl rsa -in {$privateKeyPath} -pubout -out {$publicKeyPath} > /dev/null 2>&1";
        exec($publicKeyCommand);

        echo 'RSA ключи успешно созданы!';

    }
}
