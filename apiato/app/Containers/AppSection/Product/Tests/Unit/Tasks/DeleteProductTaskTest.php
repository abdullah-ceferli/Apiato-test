<?php

namespace App\Containers\AppSection\Product\Tests\Unit\Tasks;

use App\Containers\AppSection\Product\Models\Product;
use App\Containers\AppSection\Product\Tasks\DeleteProductTask;
use App\Containers\AppSection\Product\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DeleteProductTask::class)]
final class DeleteProductTaskTest extends UnitTestCase
{
    public function testDeleteProduct(): void
    {
        $product = Product::factory()->createOne();

        $result = app(DeleteProductTask::class)->run($product->id);

        $this->assertEquals(1, $result);
    }
}
