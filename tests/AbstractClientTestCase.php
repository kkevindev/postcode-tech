<?php

namespace Kkevindev\PostcodeTech\Tests;

use Kkevindev\PostcodeTech\Client;
use Kkevindev\PostcodeTech\Exceptions\HttpException;
use Kkevindev\PostcodeTech\Exceptions\PostcodeNotFoundException;
use Kkevindev\PostcodeTech\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

abstract class AbstractClientTestCase extends TestCase
{
    abstract protected function getClient(string $token): Client;

    public function testValid200ResponseReturnsResult(): void
    {
        $expectedStreet = 'Nieuwezijds Voorburgwal';
        $expectedCity = 'Amsterdam';

        $result = $this->getClient('demo')->get('1012 RJ', 147);

        self::assertEquals($expectedStreet, $result['street']);
        self::assertEquals($expectedCity, $result['city']);
    }

    public function test401ResponseThrowsException(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(
            <<<HTML
            Unauthorized: {
                "message": "Unauthorized"
            }
            HTML
        );

        $this->getClient('invalid-token')->get('0000AA', 401);
    }

    public function test404ResponseThrowsException(): void
    {
        $this->expectException(PostcodeNotFoundException::class);
        $this->expectExceptionMessage('No results found for the given postcode and number.: {"message":"No result for this combination."}');

        $this->getClient('demo')->get('0000AA', 404);
    }

    public function test422ResponseThrowsExceptionWhenMalformedInput(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The request data was invalid.: {"message":"The given data was invalid.","errors":{"postcode":["Postcode should be formatted `1111AA` or `1111 AA`."]}}');

        $this->getClient('demo')->get('X', 422);
    }
}
