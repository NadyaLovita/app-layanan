<?php

namespace Tests\Feature;

use App\Models\User;
use BezhanSalleh\LanguageSwitch\Http\Livewire\LanguageSwitchComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_admin_login_page_renders_successfully(): void
    {
        $response = $this->get('/admin/login');

        $response->assertSuccessful();
    }

    public function test_authenticated_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertSuccessful();
    }

    public function test_authenticated_admin_can_access_all_resource_pages(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $routes = [
            '/admin/districts',
            '/admin/villages',
            '/admin/service-areas',
            '/admin/locations',
            '/admin/vehicles',
            '/admin/drivers',
            '/admin/issue-types',
            '/admin/users',
            '/admin/operation-plans',
            '/admin/service-realizations',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($admin)->get($route);
            $response->assertSuccessful();
        }
    }

    public function test_language_switch_renders_on_admin_pages(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertSuccessful();
        $response->assertSeeLivewire('language-switch-component');
    }

    public function test_admin_can_switch_language_locale(): void
    {
        Livewire::test(LanguageSwitchComponent::class)
            ->call('changeLocale', 'id');

        $this->assertEquals('id', session('locale'));
    }
}
