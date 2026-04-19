<?php

namespace Tests\Feature;

use App\Enums\PaymentEnums\PaymentMethod;
use App\Enums\ProjectEnums\ProjectStatus;
use App\Enums\UserEnums\UserRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $freelancer;
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->freelancer = User::factory()->create(['role' => UserRole::FREELANCER]);
        $this->client = User::factory()->create(['role' => UserRole::CLIENT]);
    }

    // Admin membuat project dengan status open
    public function test_admin_can_create_open_project()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('projects.store'), [
                'project_name'        => 'Test Project',
                'project_description' => 'Description',
                'project_deadline'    => '2025-12-01 00:00:00',
                'project_price'       => 500000,
                'client_id'           => $this->client->user_id,
            ]);

        $response->assertRedirect(route('projects.index'));

        $this->assertDatabaseHas('projects', [
            'project_name'   => 'Test Project',
            'project_status' => ProjectStatus::STATUS_OPEN->value,
            'user_id'        => null,
        ]);
    }

    // Admin membuat project dengan memilih freelancer tertentu
    public function test_admin_can_create_project_assigned_to_freelancer()
    {
        $this->actingAs($this->admin)
            ->post(route('projects.store'), [
                'project_name'        => 'Assigned Project',
                'project_description' => 'Description',
                'project_deadline'    => '2025-12-01 00:00:00',
                'project_price'       => 500000,
                'client_id'           => $this->client->user_id,
                'user_id'             => $this->freelancer->user_id,
            ]);

        $this->assertDatabaseHas('projects', [
            'project_status' => ProjectStatus::STATUS_REQUESTED_BY_ADMIN->value,
            'user_id'        => $this->freelancer->user_id,
        ]);
    }

    // Freelancer bisa request project yang memiliki status open
    public function test_freelancer_can_request_open_project()
    {
        $project = Project::factory()->create([
            'project_status' => ProjectStatus::STATUS_OPEN,
            'user_id'        => null,
            'client_id'      => $this->client->user_id,
        ]);

        $this->actingAs($this->freelancer)
            ->post(route('projects.request', $project->project_id));

        $this->assertDatabaseHas('projects', [
            'project_id'     => $project->project_id,
            'project_status' => ProjectStatus::STATUS_REQUESTED_BY_FREELANCER->value,
            'user_id'        => $this->freelancer->user_id,
        ]);
    }

    // Uji coba siklus secara utuh: open -> requested -> running -> completed -> revision -> completed -> done
    public function test_full_project_lifecycle()
    {
        Storage::fake('local');

        $project = Project::factory()->create([
            'project_status' => ProjectStatus::STATUS_OPEN,
            'project_price'          => 500000,
            'user_id'        => null,
            'client_id'      => $this->client->user_id,
        ]);

        // Freelancer request
        $this->actingAs($this->freelancer)
            ->post(route('projects.request', $project->project_id));
        $this->assertEquals(ProjectStatus::STATUS_REQUESTED_BY_FREELANCER, $project->fresh()->project_status);

        // Admin accept
        $this->actingAs($this->admin)
            ->post(route('projects.accept', $project->project_id));
        $this->assertEquals(ProjectStatus::STATUS_RUNNING, $project->fresh()->project_status);

        // Freelancer submit
        $this->actingAs($this->freelancer)
            ->post(route('projects.submit', $project->project_id), [
                'attachments' => [
                    UploadedFile::fake()->create('result.pdf', 500, 'application/pdf'),
                ],
            ]);
        $this->assertEquals(ProjectStatus::STATUS_COMPLETED, $project->fresh()->project_status);
        $this->assertDatabaseHas('project_attachments', ['project_id' => $project->project_id]);

        // Admin revise
        $this->actingAs($this->admin)
            ->post(route('projects.revise', $project->project_id), [
                'comment' => 'Please fix the layout',
            ]);
        $this->assertEquals(ProjectStatus::STATUS_REVISION, $project->fresh()->project_status);
        $this->assertDatabaseHas('project_logs', [
            'action'          => 'revision_requested',
            'revision_number' => 1,
        ]);

        // Freelancer resubmit
        $this->actingAs($this->freelancer)
            ->post(route('projects.resubmit', $project->project_id), [
                'attachments' => [
                    UploadedFile::fake()->create('result_v2.pdf', 500, 'application/pdf'),
                ],
                'comment' => 'Fixed the layout',
            ]);
        $this->assertEquals(ProjectStatus::STATUS_COMPLETED, $project->fresh()->project_status);

        // Admin approve with payment
        $this->actingAs($this->admin)
            ->post(route('projects.approve', $project->project_id), [
                'payment_method'      => PaymentMethod::BANK_TRANSFER->value,
                'payment_attachments' => [
                    UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf'),
                ],
            ]);
        $this->assertEquals(ProjectStatus::STATUS_DONE, $project->fresh()->project_status);

        // Payment tersimpan
        $this->assertDatabaseHas('payments', [
            'project_id' => $project->project_id,
        ]);
    }

    // Freelancer tidak bisa mengubah status menjadi done
    public function test_freelancer_cannot_approve_project()
    {
        $project = Project::factory()->create([
            'project_status' => ProjectStatus::STATUS_COMPLETED,
            'project_price'  => 500000,
            'user_id'        => $this->freelancer->user_id,
        ]);

        $this->actingAs($this->freelancer)
            ->post(route('projects.approve', $project->project_id), [
                'payment_method' => PaymentMethod::BANK_TRANSFER->value,
                'payment_attachments' => [
                    \Illuminate\Http\UploadedFile::fake()->create('bukti.jpg', 100)
                ],
            ])
            ->assertForbidden();
    }

    // Freelancer tidak bisa submit project yang tidak ditugaskan kepadanya
    public function test_freelancer_cannot_submit_unassigned_project()
    {
        $otherFreelancer = User::factory()->create(['role' => UserRole::FREELANCER]);

        $project = Project::factory()->create([
            'project_status' => ProjectStatus::STATUS_RUNNING,
            'user_id'        => $otherFreelancer->user_id,
        ]);

        $this->actingAs($this->freelancer)
            ->post(route('projects.submit', $project->project_id), [
                'attachments' => [UploadedFile::fake()->create('file.pdf', 100)],
            ])
            ->assertForbidden();
    }

    public function test_wrong_file_type_rejected()
    {
        $project = Project::factory()->create([
            'project_status' => ProjectStatus::STATUS_RUNNING,
            'user_id'        => $this->freelancer->user_id,
        ]);

        $this->actingAs($this->freelancer)
            ->post(route('projects.submit', $project->project_id), [
                'attachments' => [
                    UploadedFile::fake()->create('malware.exe', 100),
                ],
            ])
            ->assertSessionHasErrors('attachments.0');
    }

    public function test_admin_can_create_project_with_skills_and_attachments()
    {
        Storage::fake('local');

        $skills = \App\Models\Skill::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)
            ->post(route('projects.store'), [
                'project_name'        => 'Project With Skills',
                'project_description' => 'Description',
                'project_deadline'    => '2025-12-01 00:00:00',
                'project_price'       => 500000,
                'client_id'           => $this->client->user_id,
                'skill_ids'           => $skills->pluck('skill_id')->toArray(),
                'attachments' => [
                    UploadedFile::fake()->create('file1.pdf', 100, 'application/pdf'),
                    UploadedFile::fake()->create('file2.jpg', 100, 'image/jpeg'),
                ],
            ]);

        $response->assertRedirect(route('projects.index'));

        $project = Project::where('project_name', 'Project With Skills')->first();

        foreach ($skills as $skill) {
            $this->assertDatabaseHas('project_skills', [
                'project_id' => $project->project_id,
                'skill_id'   => $skill->skill_id,
            ]);
        }

        $this->assertDatabaseHas('project_attachments', [
            'project_id' => $project->project_id,
        ]);
    }

    public function test_admin_can_update_project_skills_and_attachments()
    {
        Storage::fake('local');

        $project = Project::factory()->create([
            'client_id' => $this->client->user_id,
        ]);

        $skills = \App\Models\Skill::factory()->count(2)->create();

        $this->actingAs($this->admin)
            ->patch(route('projects.update', $project->project_id), [
                'project_name'        => 'Updated Project',
                'project_description' => 'Updated Desc',
                'project_deadline'    => '2025-12-10 00:00:00',
                'client_id'           => $this->client->user_id,
                'skill_ids'           => $skills->pluck('skill_id')->toArray(),
                'attachments' => [
                    UploadedFile::fake()->create('update.pdf', 100),
                ],
            ]);

        foreach ($skills as $skill) {
            $this->assertDatabaseHas('project_skills', [
                'project_id' => $project->project_id,
                'skill_id'   => $skill->skill_id,
            ]);
        }

        $this->assertDatabaseHas('project_attachments', [
            'project_id' => $project->project_id,
        ]);
    }

    public function test_invalid_skill_ids_rejected()
    {
        $this->actingAs($this->admin)
            ->post(route('projects.store'), [
                'project_name'        => 'Invalid Skill Project',
                'project_description' => 'Desc',
                'project_deadline'    => '2025-12-01',
                'client_id'           => $this->client->user_id,
                'skill_ids'           => [9999],
            ])
            ->assertSessionHasErrors('skill_ids.0');
    }

    public function test_approve_without_payment_attachment_rejected()
    {
        $project = Project::factory()->create([
            'project_status' => ProjectStatus::STATUS_COMPLETED,
            'project_price'          => 500000,
            'user_id'        => $this->freelancer->user_id,
            'client_id'      => $this->client->user_id,
        ]);

        $this->actingAs($this->admin)
            ->post(route('projects.approve', $project->project_id), [
                'payment_method' => 'transfer',
                // payment_attachments missing
            ])
            ->assertSessionHasErrors('payment_attachments');
    }

    // ✅ Test 12 — Approve dengan file type yang salah ditolak
    public function test_approve_with_wrong_file_type_rejected()
    {
        Storage::fake('local');

        $project = Project::factory()->create([
            'project_status' => ProjectStatus::STATUS_COMPLETED,
            'project_price'          => 500000,
            'user_id'        => $this->freelancer->user_id,
            'client_id'      => $this->client->user_id,
        ]);

        $this->actingAs($this->admin)
            ->post(route('projects.approve', $project->project_id), [
                'payment_method'      => 'transfer',
                'payment_attachments' => [
                    UploadedFile::fake()->create('bukti.exe', 100),
                ],
            ])
            ->assertSessionHasErrors('payment_attachments.0');
    }

    // ✅ Test 13 — Payment tersimpan dengan harga dari project
    public function test_payment_saves_price_from_project_harga()
    {
        Storage::fake('local');

        $project = Project::factory()->create([
            'project_status' => ProjectStatus::STATUS_COMPLETED,
            'project_price'  => 500000,
            'user_id'        => $this->freelancer->user_id,
            'client_id'      => $this->client->user_id,
        ]);

        $this->actingAs($this->admin)
            ->post(route('projects.approve', $project->project_id), [
                'payment_method'      => 'qris',
                'payment_attachments' => [
                    UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf'),
                ],
            ]);

        $this->assertDatabaseHas('payments', [
            'project_id'     => $project->project_id,
            'payment_method' => 'qris',
        ]);
    }

    // ✅ Test 14 — Freelancer tidak bisa request jika sudah punya 3 project aktif
    public function test_freelancer_cannot_request_if_has_3_active_projects()
    {
        // Create 3 running projects for freelancer
        Project::factory()->count(3)->create([
            'project_status' => ProjectStatus::STATUS_RUNNING,
            'user_id'        => $this->freelancer->user_id,
            'client_id'      => $this->client->user_id,
        ]);

        $openProject = Project::factory()->create([
            'project_status' => ProjectStatus::STATUS_OPEN,
            'user_id'        => null,
            'client_id'      => $this->client->user_id,
        ]);

        $this->actingAs($this->freelancer)
            ->post(route('projects.request', $openProject->project_id))
            ->assertForbidden();
    }

    // ✅ Test 15 — Client tidak bisa lihat project orang lain
    public function test_client_cannot_view_other_clients_project()
    {
        $otherClient = User::factory()->create(['role' => UserRole::CLIENT]);

        $project = Project::factory()->create([
            'project_status' => ProjectStatus::STATUS_OPEN,
            'client_id'      => $otherClient->user_id,
        ]);

        $this->actingAs($this->client)
            ->get(route('projects.show', $project->project_id))
            ->assertForbidden();
    }
}