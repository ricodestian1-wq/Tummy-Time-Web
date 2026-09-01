<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MenuImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_can_store_custom_image_url(): void
    {
        $this->assertTrue(Schema::hasColumn('menus', 'image_url'));

        $category = Category::create(['name' => 'Ayam Crispy']);

        $menu = Menu::create([
            'category_id' => $category->id,
            'name' => 'Ayam Crispy + Nasi',
            'description' => 'Crispy dan menggoda',
            'price' => 15000,
            'is_available' => true,
            'stock' => 12,
            'image_url' => 'https://example.com/images/ayam-crispy.jpg',
        ]);

        $this->assertSame('https://example.com/images/ayam-crispy.jpg', $menu->image_url);
    }

    public function test_admin_can_create_category(): void
    {
        $admin = Admin::create([
            'username' => 'admin',
            'password' => 'secret123',
        ]);

        $this->actingAs($admin, 'admin')
            ->post('/admin/category/save', ['name' => 'Paket Hemat'])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('name', 'Paket Hemat');

        $this->assertDatabaseHas('categories', ['name' => 'Paket Hemat']);
    }
}
