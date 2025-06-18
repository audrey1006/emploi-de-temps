<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'create:admin {email} {password}';
    protected $description = 'Créer un utilisateur administrateur';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $admin = User::create([
            'name' => 'Administrateur',
            'email' => $email,
            'password' => Hash::make($password),
            'phone' => '+1234567890',
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->info('Administrateur créé avec succès !');
        $this->info('Email: ' . $admin->email);
        $this->info('Rôle: ' . $admin->role);
    }
}