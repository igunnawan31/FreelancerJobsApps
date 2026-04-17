<?php

namespace Tests\Feature;

use App\Enums\UserEnums\UserRole;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserFlowTest extends TestCase
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

    public function test_admin_can_view_user_list()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('users.index'));

        $response->assertOk();
        $response->assertViewIs('users.index');
    }

    public function test_freelancer_cannot_view_user_list()
    {
        $this->actingAs($this->freelancer)
            ->get(route('users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_any_user_profile()
    {
        $this->actingAs($this->admin)
            ->get(route('users.show', $this->freelancer->user_id))
            ->assertOk()
            ->assertViewIs('users.show');
    }

    public function test_user_can_view_own_profile()
    {
        $this->actingAs($this->freelancer)
            ->get(route('users.show', $this->freelancer->user_id))
            ->assertOk();
    }

    public function test_user_cannot_view_other_user_profile()
    {
        $this->actingAs($this->freelancer)
            ->get(route('users.show', $this->client->user_id))
            ->assertForbidden();
    }

    public function test_admin_can_create_user()
    {
        $this->actingAs($this->admin)
            ->post(route('users.store'), [
                'name'                  => 'New Freelancer',
                'email'                 => 'new@test.com',
                'password'              => 'password123',
                'password_confirmation' => 'password123',
                'role'                  => UserRole::FREELANCER->value,
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'new@test.com',
            'role'  => UserRole::FREELANCER->value,
        ]);
    }

    public function test_freelancer_cannot_create_user()
    {
        $this->actingAs($this->freelancer)
            ->post(route('users.store'), [
                'name'                  => 'Hacker',
                'email'                 => 'hacker@test.com',
                'password'              => 'password123',
                'password_confirmation' => 'password123',
                'role'                  => UserRole::ADMIN->value,
            ])
            ->assertForbidden();
    }

    public function test_admin_can_update_user_profile()
    {
        $this->actingAs($this->admin)
            ->put(route('users.update', $this->freelancer->user_id), [
                'name'  => 'Updated Name',
                'email' => $this->freelancer->email,
                'role'  => UserRole::FREELANCER->value,
            ])
            ->assertRedirect(route('users.show', $this->freelancer->user_id));

        $this->assertDatabaseHas('users', [
            'user_id' => $this->freelancer->user_id,
            'name'    => 'Updated Name',
        ]);
    }

    public function test_user_can_update_own_profile()
    {
        $this->actingAs($this->freelancer)
            ->put(route('users.update', $this->freelancer->user_id), [
                'name'  => 'My New Name',
                'email' => $this->freelancer->email,
                'role'  => UserRole::FREELANCER->value,
            ])
            ->assertRedirect(route('users.show', $this->freelancer->user_id));

        $this->assertDatabaseHas('users', ['name' => 'My New Name']);
    }

    public function test_user_can_update_own_password()
    {
        $this->actingAs($this->freelancer)
            ->put(route('users.password.update', $this->freelancer->user_id), [
                'current_password' => 'password',
                'password'         => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ])
            ->assertRedirect();

        $this->assertTrue(
            Hash::check('newpassword123', $this->freelancer->fresh()->password)
        );
    }

    public function test_admin_cannot_change_other_user_password()
    {
        $this->actingAs($this->admin)
            ->put(route('users.password.update', $this->freelancer->user_id), [
                'current_password'      => 'password',
                'password'              => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ])
            ->assertForbidden();
    }

    public function test_profile_picture_replaces_old_one_on_update()
    {
        Storage::fake('public');

        $oldPath = 'profile_pictures/old.jpg';
        Storage::disk('public')->put($oldPath, 'old content');
        $this->freelancer->forceFill(['profile_picture' => $oldPath])->save();

        $this->actingAs($this->freelancer)
            ->put(route('users.update', $this->freelancer->user_id), [
                'name'            => $this->freelancer->name,
                'email'           => $this->freelancer->email,
                'role'            => UserRole::FREELANCER->value,
                'profile_picture' => UploadedFile::fake()->create('new.jpg', 100, 'image/jpeg'),
            ])
            ->assertRedirect();

        $this->assertFalse(Storage::disk('public')->exists($oldPath));
        $this->assertNotNull($this->freelancer->fresh()->profile_picture);
        $this->assertNotEquals($oldPath, $this->freelancer->fresh()->profile_picture);
    }

    // ✅ Test 13 — Skills are synced on user update
    public function test_skills_are_synced_on_update()
    {
        $skills = Skill::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->put(route('users.update', $this->freelancer->user_id), [
                'name'      => $this->freelancer->name,
                'email'     => $this->freelancer->email,
                'role'      => UserRole::FREELANCER->value,
                'skill_ids' => $skills->pluck('skill_id')->toArray(),
            ]);

        $this->assertCount(3, $this->freelancer->fresh()->skills);
    }

    // ✅ Test 14 — Admin can delete a user
    public function test_admin_can_delete_user()
    {
        $userToDelete = User::factory()->create(['role' => UserRole::FREELANCER]);

        $this->actingAs($this->admin)
            ->delete(route('users.destroy', $userToDelete->user_id))
            ->assertRedirect(route('users.index'));

        $this->assertSoftDeleted('users', ['user_id' => $userToDelete->user_id]);
    }

    // ✅ Test 15 — Freelancer cannot delete a user
    public function test_freelancer_cannot_delete_user()
    {
        $this->actingAs($this->freelancer)
            ->delete(route('users.destroy', $this->client->user_id))
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['user_id' => $this->client->user_id]);
    }
}
