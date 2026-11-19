<?php

namespace App\Containers\AppSection\Product\Tests\Unit\Tasks;

use App\Containers\AppSection\Product\Models\Product;
use App\Containers\AppSection\Product\Tasks\ListProductsTask;
use App\Containers\AppSection\Product\Tests\UnitTestCase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ListProductsTask::class)]
final class ListProductsTaskTest extends UnitTestCase
{
    public function testListProducts(): void
    {
        Product::factory()->count(3)->create();

        $foundProducts = app(ListProductsTask::class)->run();

        $this->assertCount(3, $foundProducts);
        $this->assertInstanceOf(LengthAwarePaginator::class, $foundProducts);
    }
}
