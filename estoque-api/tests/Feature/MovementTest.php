<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_movement()
    {
        $user = User::factory()->create([
            'email' => 'admin@admin.com',
            'password' => bcrypt('12345678'),
        ]);
        
        Sanctum::actingAs($user);

        $category = Category::create(['name' => 'Categoria Unitária']);
        $supplier = Supplier::create(['name' => 'Fornecedor Unitário', 'cnpj' => '122123456789', 'contato' => '65465877' ]);

        $product_data = [
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

        $product = Product::create($product_data);

        $data = [
            'product_id' => $product->id,
            'user_id' => $user->id,           
            'type' => 'entry',
            'quantity' => 200,
            'price' => 550.50,            
            'reason' => 'promoção 50%',
        ];

        $response = $this->postJson('/api/movements', $data);

        $this->assertDatabaseHas('movements', ['reason' => 'promoção 50%']);
        $response->assertStatus(201)->assertJsonFragment(['reason' => 'promoção 50%']);
    }
}

