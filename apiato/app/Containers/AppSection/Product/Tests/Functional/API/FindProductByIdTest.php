<?php

namespace App\Containers\AppSection\Product\Tests\Functional\API;

use App\Containers\AppSection\Product\Models\Product;
use App\Containers\AppSection\Product\Tests\Functional\ApiTestCase;
use App\Containers\AppSection\Product\UI\API\Controllers\FindProductByIdController;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
final class FindProductByIdTest extends ApiTestCase
{
    public function testFindProduct(): void
    {
        $this->actingAs(User::factory()->createOne());
        $product = Product::factory()->createOne();

        $response = $this->getJson(action(FindProductByIdController::class, ['id' => $product->getHashedKey()]));

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) =>
                $json->has('data')
                    ->where('data.id', $product->getHashedKey())
                    ->etc()
        );
    }
}
