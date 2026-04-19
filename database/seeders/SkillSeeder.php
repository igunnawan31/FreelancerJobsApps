<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Skill::create([
            'skill_name' => 'Illus',
            'skill_description' => 'you have to listen to your mother',
        ]);

        Skill::create([
            'skill_name' => '3d',
            'skill_description' => 'end this now',
        ]);
    }
}
