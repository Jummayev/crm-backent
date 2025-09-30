<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_user_can_be_assigned_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Operator');

        $this->assertTrue($user->hasRole('Operator'));
    }

    public function test_admin_has_all_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $this->assertTrue($user->hasRole('Admin'));
        $this->assertTrue($user->can('manage-users'));
        $this->assertTrue($user->can('delete-customer'));
    }

    public function test_manager_has_specific_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Manager');

        $this->assertTrue($user->hasRole('Manager'));
        $this->assertTrue($user->can('view-reports'));
        $this->assertTrue($user->can('edit-customer'));
        $this->assertFalse($user->can('manage-users'));
    }

    public function test_operator_has_limited_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Operator');

        $this->assertTrue($user->hasRole('Operator'));
        $this->assertTrue($user->can('view-customers'));
        $this->assertTrue($user->can('create-order'));
        $this->assertFalse($user->can('delete-order'));
        $this->assertFalse($user->can('view-reports'));
    }

    public function test_user_can_have_multiple_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole(['Operator', 'Manager']);

        $this->assertTrue($user->hasRole('Operator'));
        $this->assertTrue($user->hasRole('Manager'));
    }

    public function test_operator_does_not_have_admin_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Operator');

        $this->assertFalse($user->can('manage-users'));
        $this->assertFalse($user->can('manage-roles'));
    }
}
