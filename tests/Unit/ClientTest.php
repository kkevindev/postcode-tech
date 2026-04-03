<?php

declare(strict_types=1);

namespace Hpolthof\PostcodeTech\Tests\Unit;

use Hpolthof\PostcodeTech\Client;
use Hpolthof\PostcodeTech\Tests\AbstractClientTestCase;
use Hpolthof\PostcodeTech\Tests\Mock\MockResponseHttpClient;

final class ClientTest extends AbstractClientTestCase
{
    protected function getClient(string $token): Client
    {
        return new Client($token, new MockResponseHttpClient());
    }
}
