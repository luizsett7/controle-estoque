<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Mockery;
use Tests\TestCase;

class ProductUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_product_can_be_created_with_mock()
    {
        $mockCategory = Mockery::mock(Category::class);
        $mockCategory->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn((object) ['id' => 1, 'name' => 'Categoria Unitária']);

        $mockSupplier = Mockery::mock(Supplier::class);
        $mockSupplier->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn((object) ['id' => 1, 'name' => 'Fornecedor Unitário']);

        $mockProduct = Mockery::mock(Product::class);
        $mockProduct->shouldReceive('create')
            ->once()
            ->with([
                'name' => 'Produto Unitário',
                'code' => 'F100',
                'description' => 'Descrição Unitária',
                'category_id' => 1,
                'supplier_id' => 1,
                'cost_price' => 150,
                'sale_price' => 200,
                'min_stock' => 20,
                'stock' => 30,
                'expiration_date' => '2024-11-28',
            ])
            ->andReturn((object) [
                'name' => 'Produto Unitário',
                'code' => 'F100',
                'description' => 'Descrição Unitária',
                'category_id' => 1,
                'supplier_id' => 1,
                'cost_price' => 150,
                'sale_price' => 200,
                'min_stock' => 20,
                'stock' => 30,
                'expiration_date' => '2024-11-28',
            ]);

        $category = $mockCategory->find(1);
        $supplier = $mockSupplier->find(1);

        $this->assertEquals(1, $category->id);
        $this->assertEquals(1, $supplier->id);

        $product = $mockProduct->create([
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
        ]);

        $this->assertEquals('Produto Unitário', $product->name);
        $this->assertEquals('F100', $product->code);
        $this->assertEquals(1, $product->category_id);
        $this->assertEquals(1, $product->supplier_id);
        $this->assertEquals(150, $product->cost_price);
        $this->assertEquals(200, $product->sale_price);
        $this->assertEquals(20, $product->min_stock);
        $this->assertEquals(30, $product->stock);
        $this->assertEquals('2024-11-28', $product->expiration_date);
    }
}
