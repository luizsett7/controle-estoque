<?php

namespace Tests\Unit;

use App\Models\Movement;
use App\Models\User;
use App\Models\Product;
use Mockery;
use Tests\TestCase;

class MovementUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_movement_can_be_created_with_mock()
    {
        $mockUser = Mockery::mock(User::class);
        $mockUser->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn((object) ['id' => 1, 'name' => 'Admin']);

        $mockProduct = Mockery::mock(Product::class);
        $mockProduct->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn((object) ['id' => 1, 'name' => 'Produto Unitário']);

        $mockMovement = Mockery::mock(Movement::class);
        $mockMovement->shouldReceive('create')
            ->once()
            ->with([            
                'product_id' => 1,    
                'user_id' => 1,                 
                'type' => 'entry',
                'quantity' => 200,
                'price' => 550.50,
                'reason' => 'promoção',                
            ])
            ->andReturn((object) [
                'product_id' => 1,    
                'user_id' => 1,                 
                'type' => 'entry',
                'quantity' => 200,
                'price' => 550.50,
                'reason' => 'promoção',
            ]);

        $user = $mockUser->find(1);
        $product = $mockProduct->find(1);

        $this->assertEquals(1, $user->id);
        $this->assertEquals(1, $product->id);

        $movement = $mockMovement->create([
                'product_id' => 1,    
                'user_id' => 1,                 
                'type' => 'entry',
                'quantity' => 200,
                'price' => 550.50,
                'reason' => 'promoção',
        ]);

        $this->assertEquals(1, $movement->product_id);
        $this->assertEquals(1, $movement->user_id);
        $this->assertEquals('entry', $movement->type);
        $this->assertEquals(200, $movement->quantity);
        $this->assertEquals(550.50, $movement->price);
        $this->assertEquals('promoção', $movement->reason);       
    }
}
