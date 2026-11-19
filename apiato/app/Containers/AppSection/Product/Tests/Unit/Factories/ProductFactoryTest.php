<?php

namespace App\Containers\AppSection\Product\Tests\Unit\Factories;

use App\Containers\AppSection\Product\Data\Factories\ProductFactory;
use App\Containers\AppSection\Product\Models\Product;
use App\Containers\AppSection\Product\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ProductFactory::class)]
final class ProductFactoryTest extends UnitTestCase
{
    public function testCreateProduct(): void
    {
        $product = Product::factory()->make();

        $this->assertInstanceOf(Product::class, $product);
    }
}
