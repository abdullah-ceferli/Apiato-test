<?php

namespace App\Containers\AppSection\Product\Tests\Functional\API;

use App\Containers\AppSection\Product\Models\Product;
use App\Containers\AppSection\Product\Tests\Functional\ApiTestCase;
use App\Containers\AppSection\Product\UI\API\Controllers\ListProductsController;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
final class ListProductsTest extends ApiTestCase
{
    public function testListProductsByAdmin(): void
    {
        $this->actingAs(User::factory()->createOne());
        Product::factory()->count(2)->create();

        $response = $this->getJson(action(ListProductsController::class));

        $response->assertOk();
        $response->assertJson(
            static fn (AssertableJson $json) =>
                $json->has('data', 2)
                    ->etc()
        );
    }
}
