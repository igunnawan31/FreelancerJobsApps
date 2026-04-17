<?php

namespace Tests\Feature;

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
            'user_id'        => null,
            'client_id'      => $this->client->user_id,
        ]);

        $this->actingAs($this->freelancer)
            ->post(route('projects.request', $project->project_id));
        $this->assertEquals(ProjectStatus::STATUS_REQUESTED_BY_FREELANCER, $project->fresh()->project_status);

        $this->actingAs($this->admin)
            ->post(route('projects.accept', $project->project_id));
        $this->assertEquals(ProjectStatus::STATUS_RUNNING, $project->fresh()->project_status);

        $this->actingAs($this->freelancer)
            ->post(route('projects.submit', $project->project_id), [
                'attachments' => [
                    UploadedFile::fake()->create('result.pdf', 500, 'application/pdf'),
                ],
            ]);
        $this->assertEquals(ProjectStatus::STATUS_COMPLETED, $project->fresh()->project_status);
        $this->assertDatabaseHas('project_attachments', ['project_id' => $project->project_id]);

        $this->actingAs($this->admin)
            ->post(route('projects.revise', $project->project_id), [
                'comment' => 'Please fix the layout',
            ]);

        $this->assertEquals(ProjectStatus::STATUS_REVISION, $project->fresh()->project_status);
        $this->assertDatabaseHas('project_logs', [
            'action'          => 'revision_requested',
            'revision_number' => 1,
        ]);

        $this->actingAs($this->freelancer)
            ->post(route('projects.resubmit', $project->project_id), [
                'attachments' => [
                    UploadedFile::fake()->create('result_v2.pdf', 500, 'application/pdf'),
                ],
                'comment' => 'Fixed the layout',
            ]);
        $this->assertEquals(ProjectStatus::STATUS_COMPLETED, $project->fresh()->project_status);

        $this->actingAs($this->admin)
            ->post(route('projects.approve', $project->project_id));
        $this->assertEquals(ProjectStatus::STATUS_DONE, $project->fresh()->project_status);
    }

    // Freelancer tidak bisa mengubah status menjadi done
    public function test_freelancer_cannot_approve_project()
    {
        $project = Project::factory()->create([
            'project_status' => ProjectStatus::STATUS_COMPLETED,
            'user_id'        => $this->freelancer->user_id,
        ]);

        $this->actingAs($this->freelancer)
            ->post(route('projects.approve', $project->project_id))
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
}