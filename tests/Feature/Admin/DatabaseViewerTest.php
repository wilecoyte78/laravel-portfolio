<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DatabaseViewerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.database.index'))
            ->assertRedirect(route('login'));
    }

    public function test_non_admins_are_forbidden(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('admin.database.index'))
            ->assertForbidden();
    }

    public function test_admin_can_list_tables(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.database.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Database/Index')
                ->has('tables')
            );
    }

    public function test_admin_can_view_table_rows_with_secrets_masked(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.database.show', 'users'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Database/Show')
                ->where('table', 'users')
                ->where('rows.0.email', $admin->email)
                ->where('rows.0.password', '••••••')
            );
    }

    public function test_hidden_and_unknown_tables_return_404(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.database.show', 'sessions'))->assertNotFound();
        $this->actingAs($admin)->get(route('admin.database.show', 'nope; DROP TABLE users'))->assertNotFound();
    }

    public function test_write_methods_are_not_routable(): void
    {
        $this->actingAs($this->admin());

        $this->post('/admin/database/users')->assertStatus(405);
        $this->put('/admin/database/users')->assertStatus(405);
        $this->delete('/admin/database/users')->assertStatus(405);
    }
}
