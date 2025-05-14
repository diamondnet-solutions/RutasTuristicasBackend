<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Optionally disable exception handling for easier debugging
        $this->withoutExceptionHandling();

        // Ensure roles and permissions tables are available
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
        Permission::create(['name' => 'view dashboard']);
    }

    /** @test */
    public function summary_returns_empty_counts_when_no_data()
    {
        $response = $this->getJson('/api/dashboard/summary');

        $response->assertStatus(200)
                 ->assertJson([ 
                     'success' => true,
                     'data' => [
                         'total_users' => 0,
                         'active_users' => 0,
                         'inactive_users' => 0,
                         'users_by_role' => [
                             ['role' => 'admin', 'count' => 0],
                             ['role' => 'user',  'count' => 0],
                         ],
                         'total_roles' => 2,
                         'total_permissions' => 1,
                         'recent_users' => [],
                     ]
                 ]);
    }

    /** @test */
    public function summary_returns_correct_counts_and_users_by_role()
    {
        // Create users
        $u1 = User::factory()->create(['active' => true]);
        $u2 = User::factory()->create(['active' => true]);
        $u3 = User::factory()->create(['active' => false]);

        // Assign roles
        $u1->assignRole('admin');
        $u2->assignRole('user');

        $response = $this->getJson('/api/dashboard/summary');

        $response->assertStatus(200)
                 ->assertJsonPath('data.total_users', 3)
                 ->assertJsonPath('data.active_users', 2)
                 ->assertJsonPath('data.inactive_users', 1)
                 ->assertJsonFragment(['role' => 'admin', 'count' => 1])
                 ->assertJsonFragment(['role' => 'user',  'count' => 1])
                 ->assertJsonPath('data.total_roles', 2)
                 ->assertJsonPath('data.total_permissions', 1);
    }

    /** @test */
    public function summary_returns_recent_users_ordered_by_creation_date()
    {
        // Create 6 users at different dates
        foreach (range(1, 6) as $i) {
            User::factory()->create([
                'created_at' => Carbon::now()->subDays($i),
                'active'     => true,
            ]);
        }

        $response = $this->getJson('/api/dashboard/summary');

        $response->assertStatus(200);

        $recent = $response->json('data.recent_users');
        // Should return only 5 users
        $this->assertCount(5, $recent);

        // Check order: first element is the most recent
        $dates = array_column($recent, 'created_at');
        $sorted = $dates;
        rsort($sorted);
        $this->assertSame($sorted, $dates);
    }
}
