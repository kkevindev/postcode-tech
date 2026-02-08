<?php

declare(strict_types=1);

namespace Hpolthof\PostcodeTech\Tests\Mock;

use Hpolthof\AssertReturnValue\Assert;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class MockResponseHttpClient extends MockHttpClient
{
    public function __construct()
    {
        parent::__construct(static function (string $method, string $url, array $options): MockResponse {
            $url = parse_url($url);

            $responseDir = sprintf('%s/%s', __DIR__, 'Responses');

            $responseFileName = ltrim(
                str_replace(
                    '/',
                    '_',
                    sprintf(
                        '%s_%s',
                        Assert::stringNotEmpty($url['path'] ?? ''),
                        Assert::stringNotEmpty($url['query'] ?? ''),
                    ),
                ),
                '_',
            );

            $responseFilePath = sprintf('%s/%s', $responseDir, $responseFileName);
            if (!file_exists($responseFilePath)) {
                touch($responseFilePath);
                exit(sprintf("Expected mock response file '%s' to exist.", $responseFilePath));
            }

            $meta = [];
            if (file_exists($responseFilePath.'.meta')) {
                $meta = parse_ini_file($responseFilePath.'.meta', false, INI_SCANNER_TYPED) ?: [];
            }

            return new MockResponse(
                Assert::string(file_get_contents($responseFilePath)),
                $meta,
            );
        });
    }
}
