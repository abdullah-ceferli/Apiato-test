<?php

namespace App\Containers\AppSection\Product\Tests\Functional\API;

use App\Containers\AppSection\Product\Models\Product;
use App\Containers\AppSection\Product\Tests\Functional\ApiTestCase;
use App\Containers\AppSection\Product\UI\API\Controllers\DeleteProductController;
use App\Containers\AppSection\User\Models\User;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
final class DeleteProductTest extends ApiTestCase
{
    public function testDeleteExistingProduct(): void
    {
        $this->actingAs(User::factory()->createOne());
        $product = Product::factory()->createOne();

        $response = $this->deleteJson(action(DeleteProductController::class, ['id' => $product->getHashedKey()]));

        $response->assertNoContent();
    }
}
