<?php

namespace App\Containers\AppSection\Product\Tests\Unit\Tasks;

use App\Containers\AppSection\Product\Tasks\CreateProductTask;
use App\Containers\AppSection\Product\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CreateProductTask::class)]
final class CreateProductTaskTest extends UnitTestCase
{
    public function testCreateProduct(): void
    {
        $data = [];

        $product = app(CreateProductTask::class)->run($data);

        $this->assertModelExists($product);
    }
}
