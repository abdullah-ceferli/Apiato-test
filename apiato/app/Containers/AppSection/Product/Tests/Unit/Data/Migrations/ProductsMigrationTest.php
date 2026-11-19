<?php

namespace App\Containers\AppSection\Product\Tests\Unit\Data\Migrations;

use App\Containers\AppSection\Product\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
final class ProductsMigrationTest extends UnitTestCase
{
    public function testProductsTableHasExpectedColumns(): void
    {
        $columns = [
            'id' => 'int8',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
        ];

        $this->assertDatabaseTable('products', $columns);
    }
}
