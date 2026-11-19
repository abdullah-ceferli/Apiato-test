<?php

namespace App\Containers\AppSection\Product\Tests\Unit\Tasks;

use App\Containers\AppSection\Product\Models\Product;
use App\Containers\AppSection\Product\Tasks\FindProductByIdTask;
use App\Containers\AppSection\Product\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FindProductByIdTask::class)]
final class FindProductByIdTaskTest extends UnitTestCase
{
    public function testFindProductById(): void
    {
        $product = Product::factory()->createOne();

        $foundProduct = app(FindProductByIdTask::class)->run($product->id);

        $this->assertEquals($product->id, $foundProduct->id);
    }
}
