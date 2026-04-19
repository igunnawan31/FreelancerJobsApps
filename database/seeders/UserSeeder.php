<?php

namespace Database\Seeders;

use App\Enums\UserEnums\UserRole;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $skills = Skill::all();

        $admin = User::create([
            'name' => 'Admin System',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => UserRole::ADMIN,
            'phone_number' => '08123456789',
        ]);

        $freelancer = User::create([
            'name' => 'Aghanim Developer',
            'email' => 'freelancer@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => UserRole::FREELANCER,
            'phone_number' => '08987654321',
            'portfolio' => 'https://github.com/aghanim31', 
        ]);

        if ($skills->isNotEmpty()) {
            $freelancer->skills()->sync($skills->pluck('skill_id')->toArray());
        }

        User::create([
            'name' => 'Undip Digital Library',
            'email' => 'client@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => UserRole::CLIENT,
            'phone_number' => '024123456',
        ]);
    }
}
