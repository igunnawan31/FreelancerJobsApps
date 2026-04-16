<?php

namespace Database\Seeders;

use App\Enums\UserEnums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'role' => UserRole::ADMIN,
        ]);

        User::create([
            'name' => 'Freelancer',
            'email' => 'freelancer@gmail.com',
            'password' => bcrypt('12345678'),
            'role' => UserRole::FREELANCER,
        ]);
}
}
