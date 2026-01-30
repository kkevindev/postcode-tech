<?php

declare(strict_types=1);

namespace Kkevindev\PostcodeTech\Tests\Unit;

use Kkevindev\PostcodeTech\Client;
use Kkevindev\PostcodeTech\Tests\AbstractClientTestCase;
use Kkevindev\PostcodeTech\Tests\Mock\MockResponseHttpClient;

final class ClientTest extends AbstractClientTestCase
{
    protected function getClient(string $token): Client
    {
        return new Client($token, new MockResponseHttpClient());
    }
}
