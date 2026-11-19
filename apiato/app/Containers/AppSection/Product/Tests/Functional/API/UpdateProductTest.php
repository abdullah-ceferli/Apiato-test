<?php

namespace App\Containers\AppSection\Product\Tests\Functional\API;

use App\Containers\AppSection\Product\Models\Product;
use App\Containers\AppSection\Product\Tests\Functional\ApiTestCase;
use App\Containers\AppSection\Product\UI\API\Controllers\UpdateProductController;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
final class UpdateProductTest extends ApiTestCase
{
    // TODO: test
    public function testUpdateExistingProduct(): void
    {
        $this->actingAs(User::factory()->createOne());
        $product = Product::factory()->createOne([
            // 'some_field' => 'new_field_value',
        ]);
        $data = [
            // 'some_field' => 'new_field_value',
        ];

        $response = $this->patchJson(action(UpdateProductController::class, ['id' => $product->getHashedKey()]), $data);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) =>
                $json->has('data')
                    ->where('data.type', 'Product')
                    ->where('data.id', $product->getHashedKey())
                    // ->where('data.some_field', $data['some_field'])
                    ->etc()
        );
    }
}
