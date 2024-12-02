<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_product()
    {
        $user = User::factory()->create([
            'email' => 'admin@admin.com',
            'password' => bcrypt('12345678'),
        ]);
        
        Sanctum::actingAs($user);

        $category = Category::create(['name' => 'Categoria Unitária']);
        $supplier = Supplier::create(['name' => 'Fornecedor Unitário', 'cnpj' => '122123456789', 'contato' => '65465877' ]);

        $data = [
            'name' => 'Produto Unitário',
            'code' => 'F100',
            'description' => 'Descrição Unitária',
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'cost_price' => 150,
            'sale_price' => 200,
            'min_stock' => 20,
            'stock' => 30,
            'expiration_date' => '2024-11-28',
        ];

        $response = $this->postJson('/api/products', $data);

        $this->assertDatabaseHas('products', ['name' => 'Produto Unitário']);
        $response->assertStatus(201)->assertJsonFragment(['name' => 'Produto Unitário']);
    }
}

