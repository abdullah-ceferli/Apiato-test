<?php

namespace App\Containers\AppSection\Product\Tests\Unit\Tasks;

use App\Containers\AppSection\Product\Models\Product;
use App\Containers\AppSection\Product\Tasks\UpdateProductTask;
use App\Containers\AppSection\Product\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UpdateProductTask::class)]
final class UpdateProductTaskTest extends UnitTestCase
{
    // TODO TEST
    public function testUpdateProduct(): void
    {
        $product = Product::factory()->create([
            // 'some_field' => 'new_field_value',
        ]);
        $data = [
            // 'some_field' => 'new_field_value',
        ];

        $updatedProduct = app(UpdateProductTask::class)->run($data, $product->id);

        $this->assertEquals($product->id, $updatedProduct->id);
        // $this->assertEquals($data['some_field'], $updatedProduct->some_field);
    }
}
