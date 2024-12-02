<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_category()
    {
        $user = User::factory()->create([
            'email' => 'admin@admin.com',
            'password' => bcrypt('12345678'),
        ]);
        
        Sanctum::actingAs($user);
        
        $data = ['name' => 'Nova Categoria'];

        $response = $this->postJson('/api/categories', $data);

        $this->assertDatabaseHas('categories', ['name' => 'Nova Categoria']);
        $response->assertStatus(201)->assertJsonFragment(['name' => 'Nova Categoria']);
    }
}

