<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    protected $signature = 'make:admin';
    protected $description = 'Cria o usuário administrador inicial';

    public function handle()
    {
        $name = $this->ask('Nome completo');
        $email = $this->ask('E-mail');
        $password = $this->secret('Senha');

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'role' => 'admin',
        ]);

        $this->info('Usuário administrador criado com sucesso!');
    }
}

