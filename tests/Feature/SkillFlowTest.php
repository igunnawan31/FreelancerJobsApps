<?php

namespace Tests\Feature;

use App\Enums\UserEnums\UserRole;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $freelancer;
    protected User $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin      = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->freelancer = User::factory()->create(['role' => UserRole::FREELANCER]);
        $this->client     = User::factory()->create(['role' => UserRole::CLIENT]);
    }

    public function test_anyone_can_view_skill_list()
    {
        Skill::factory()->count(3)->create();

        $this->actingAs($this->freelancer)
            ->get(route('skills.index'))
            ->assertOk()
            ->assertViewIs('skills.index');
    }

    public function test_anyone_can_view_skill()
    {
        $skill = Skill::factory()->create();

        $this->actingAs($this->freelancer)
            ->get(route('skills.show', $skill->skill_id))
            ->assertOk()
            ->assertViewIs('skills.show');
    }

    public function test_admin_can_create_skill()
    {
        $this->actingAs($this->admin)
            ->post(route('skills.store'), [
                'skill_name'        => 'Laravel',
                'skill_description' => 'PHP Framework',
            ])
            ->assertRedirect(route('skills.index'));

        $this->assertDatabaseHas('skills', [
            'skill_name' => 'Laravel',
        ]);
    }

    public function test_freelancer_cannot_create_skill()
    {
        $this->actingAs($this->freelancer)
            ->post(route('skills.store'), [
                'skill_name' => 'Hacked Skill',
            ])
            ->assertForbidden();
    }

    public function test_client_cannot_create_skill()
    {
        $this->actingAs($this->client)
            ->post(route('skills.store'), [
                'skill_name' => 'Hacked Skill',
            ])
            ->assertForbidden();
    }

    public function test_admin_can_update_skill()
    {
        $skill = Skill::factory()->create(['skill_name' => 'Old Name']);

        $this->actingAs($this->admin)
            ->put(route('skills.update', $skill->skill_id), [
                'skill_name'        => 'New Name',
                'skill_description' => 'Updated description',
            ])
            ->assertRedirect(route('skills.index'));

        $this->assertDatabaseHas('skills', [
            'skill_id'   => $skill->skill_id,
            'skill_name' => 'New Name',
        ]);
    }

    public function test_freelancer_cannot_update_skill()
    {
        $skill = Skill::factory()->create();

        $this->actingAs($this->freelancer)
            ->put(route('skills.update', $skill->skill_id), [
                'skill_name' => 'Hacked',
            ])
            ->assertForbidden();
    }

    public function test_admin_can_delete_skill()
    {
        $skill = Skill::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('skills.destroy', $skill->skill_id))
            ->assertRedirect(route('skills.index'));

        $this->assertSoftDeleted('skills', ['skill_id' => $skill->skill_id]);
    }

    public function test_freelancer_cannot_delete_skill()
    {
        $skill = Skill::factory()->create();

        $this->actingAs($this->freelancer)
            ->delete(route('skills.destroy', $skill->skill_id))
            ->assertForbidden();

        $this->assertDatabaseHas('skills', ['skill_id' => $skill->skill_id]);
    }

    public function test_skill_name_is_required()
    {
        $this->actingAs($this->admin)
            ->post(route('skills.store'), [
                'skill_name' => '',
            ])
            ->assertSessionHasErrors('skill_name');
    }

    public function test_skill_name_must_be_unique()
    {
        Skill::factory()->create(['skill_name' => 'Laravel']);

        $this->actingAs($this->admin)
            ->post(route('skills.store'), [
                'skill_name' => 'Laravel',
            ])
            ->assertSessionHasErrors('skill_name');
    }

    public function test_search_filters_skills_by_name()
    {
        Skill::factory()->create(['skill_name' => 'Laravel']);
        Skill::factory()->create(['skill_name' => 'React']);
        Skill::factory()->create(['skill_name' => 'Figma']);

        $response = $this->actingAs($this->admin)
            ->get(route('skills.index', ['search' => 'Laravel']));

        $response->assertOk();
        $response->assertViewHas('skills', function ($skills) {
            return $skills->total() === 1
                && $skills->first()->skill_name === 'Laravel';
        });
    }
}