<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);

        // Ensure the user has the 'Admin' role
        $user = User::where('email', 'john@example.com')->first();
        $this->assertTrue($user->hasRole('Admin'));
    }

    public function test_role_assignment_to_user()
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $user = User::factory()->create();

        $user->assignRole('admin');

        $this->assertTrue($user->hasRole('admin'));
    }

    public function test_user_access_denied_without_permission()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/admin-dashboard');
        $response->assertStatus(403);
    }

    public function test_user_without_permission_cannot_create_task()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Description here',
            'priority' => 'high',
        ]);

        $response->assertStatus(403); // Forbidden
    }

    public function test_user_with_permission_can_create_task()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create tasks');
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Description here',
            'priority' => 'high',
        ]);

        $response->assertStatus(201); // Created
        $this->assertDatabaseHas('tasks', ['title' => 'Test Task']);
    }

    public function test_user_with_permission_can_access_tasks()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view tasks');

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/tasks')
            ->assertStatus(200);
    }

    public function test_user_without_permission_cannot_access_tasks()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/tasks')
            ->assertStatus(403);
    }
}
