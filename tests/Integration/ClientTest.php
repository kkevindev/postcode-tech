<?php

declare(strict_types=1);

namespace Hpolthof\PostcodeTech\Tests\Integration;

use Hpolthof\PostcodeTech\Client;
use Hpolthof\PostcodeTech\Tests\AbstractClientTestCase;
use Symfony\Component\HttpClient\HttpClient;

/**
 * This test actually calls the real API, so it can fail beyond our control.
 *
 * It serves as a check if our mocked data is still up-to-date.
 */
final class ClientTest extends AbstractClientTestCase
{
    protected function getClient(string $token): Client
    {
        return new Client($token, HttpClient::create());
    }
}
