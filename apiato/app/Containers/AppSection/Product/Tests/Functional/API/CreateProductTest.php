<?php

namespace App\Containers\AppSection\Product\Tests\Functional\API;

use App\Containers\AppSection\Product\Tests\Functional\ApiTestCase;
use App\Containers\AppSection\Product\UI\API\Controllers\CreateProductController;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
final class CreateProductTest extends ApiTestCase
{
    public function testCreateProduct(): void
    {
        $this->actingAs(User::factory()->createOne());
        $data = [
            // TODO: test
            // 'something' => 'value',
        ];

        $response = $this->postJson(action(CreateProductController::class), $data);

        $response->assertCreated();
        $response->assertJson(
            fn (AssertableJson $json) =>
                $json->has('data')
                    ->where('data.type', 'Product')
                    // ->where('data.something', $data['something'])
                    ->etc()
        );
    }
}
