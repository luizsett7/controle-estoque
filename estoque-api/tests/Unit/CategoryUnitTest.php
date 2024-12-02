<?php

namespace Tests\Unit;

use App\Models\Category;
use Mockery;
use Tests\TestCase;

class CategoryUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_category_can_be_created_with_mock()
    {
        $mockCategory = Mockery::mock(Category::class);
        $mockCategory->shouldReceive('create')
            ->once()
            ->with(['name' => 'Categoria Unitária'])
            ->andReturn((object) [
                'name' => 'Categoria Unitária'                
            ]);

        $category = $mockCategory->create([
            'name' => 'Categoria Unitária',            
        ]);

        $this->assertEquals('Categoria Unitária', $category->name);
    }
}
