<?php

namespace Database\Seeders;

use App\Enums\PaymentEnums\PaymentMethod;
use App\Enums\ProjectEnums\ProjectStatus;
use App\Enums\UserEnums\UserRole;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allSkills = Skill::all();
        $admin = User::where('role', UserRole::ADMIN)->first();
        $freelancer = User::where('role', UserRole::FREELANCER)->first();
        $client = User::where('role', UserRole::CLIENT)->first();

        $p1 = Project::create([
            'project_name' => 'E-Commerce Mobile App',
            'project_description' => 'Membangun aplikasi e-commerce menggunakan React Native.',
            'project_status' => ProjectStatus::STATUS_OPEN,
            'project_deadline' => now()->addDays(30),
            'project_price' => 5000000,
            'client_id' => $client->user_id ?? null,
        ]);
        
        $p1->skills()->sync($allSkills->pluck('skill_id'));
        $this->addDummyAttachment($p1, $admin, 'reference');


        $p2 = Project::create([
            'project_name' => 'Backend API Refactoring',
            'project_description' => 'Optimalisasi query dan refactoring controller Laravel.',
            'project_status' => ProjectStatus::STATUS_RUNNING,
            'project_deadline' => now()->addDays(14),
            'project_price' => 3500000,
            'user_id' => $freelancer->user_id ?? null,
            'client_id' => $client->user_id ?? null,
        ]);

        $skill3d = $allSkills->where('skill_name', '3d')->first();
        if ($skill3d) {
            $p2->skills()->attach($skill3d->skill_id);
        }
        $this->addDummyAttachment($p2, $admin, 'requirements');


        $p3 = Project::create([
            'project_name' => 'Landing Page Redesign',
            'project_description' => 'Redesign UI/UX landing page perusahaan.',
            'project_status' => ProjectStatus::STATUS_DONE,
            'project_deadline' => now()->subDays(5),
            'project_price' => 1500000,
            'user_id' => $freelancer->user_id ?? null,
            'client_id' => $client->user_id ?? null,
        ]);

        $illusSkill = $allSkills->where('skill_name', 'Illus')->first();
        if ($illusSkill) {
            $p3->skills()->attach($illusSkill->skill_id);
        }

        $log = $p3->projectlogs()->create([
            'actor_id' => $admin->user_id,
            'action' => 'payment and project done',
        ]);

        $p3->payments()->create([
            'project_log_id' => $log->id,
            'payment_method' => PaymentMethod::QRIS,
            'file_name' => 'receipt_sample.jpg',
            'file_path' => 'payments/receipt_sample.jpg',
            'file_type' => 'image/jpeg',
            'file_size' => 102400,
            'uploaded_by' => $admin->user_id,
            'note' => 'Pembayaran lunas via QRIS',
        ]);
    }

    private function addDummyAttachment($project, $user, $type)
    {
        if (!$user) return;

        $log = $project->projectlogs()->create([
            'actor_id' => $user->user_id,
            'action' => 'created_by_admin',
        ]);

        $project->attachments()->create([
            'project_log_id' => $log->id,
            'file_name' => "doc_{$type}.pdf",
            'file_path' => "projects/seed/{$type}.pdf",
            'file_type' => 'application/pdf',
            'file_size' => 2048,
            'uploaded_by' => $user->user_id,
        ]);
    }
}
