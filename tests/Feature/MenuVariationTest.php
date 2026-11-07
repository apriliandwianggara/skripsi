<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuVariationTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_can_create_menu_with_variations(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admin_test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);
        $category = Category::create([
            'name' => 'Coffee',
            'slug' => 'coffee',
            'order' => 1,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.menus.store'), [
            'category_id' => $category->id,
            'name' => 'Kopi Mock Test ' . uniqid(),
            'description' => 'Uji coba menu variasi',
            'price' => 20000,
            'is_available' => 1,
            'has_variations' => 1,
            'variations' => [
                ['name' => 'S', 'price' => 20000],
                ['name' => 'M', 'price' => 23000],
                ['name' => 'L', 'price' => 25000],
            ],
        ]);

        $response->assertRedirect(route('admin.menus.index'));
        $this->assertDatabaseHas('menu_variations', ['name' => 'S', 'price' => 20000]);
        $this->assertDatabaseHas('menu_variations', ['name' => 'M', 'price' => 23000]);
        $this->assertDatabaseHas('menu_variations', ['name' => 'L', 'price' => 25000]);
    }

    public function test_admin_can_create_menu_without_variations(): void
    {
        $admin = User::create([
            'name' => 'Admin Test 2',
            'username' => 'admin_test_2',
            'email' => 'admin2@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);
        $category = Category::create([
            'name' => 'Non Coffee',
            'slug' => 'non-coffee',
            'order' => 2,
        ]);
        $uniqueName = 'Kopi Reguler Test ' . uniqid();

        $response = $this->actingAs($admin)->post(route('admin.menus.store'), [
            'category_id' => $category->id,
            'name' => $uniqueName,
            'description' => 'Uji coba menu reguler tanpa variasi',
            'price' => 18000,
            'is_available' => 1,
            'has_variations' => 0,
        ]);

        $response->assertRedirect(route('admin.menus.index'));
        $menu = Menu::where('name', $uniqueName)->first();
        $this->assertNotNull($menu);
        $this->assertCount(0, $menu->variations);
    }
}
