<?php

namespace Hpolthof\PostcodeTech;

use Hpolthof\PostcodeTech\Exceptions\HttpException;
use Hpolthof\PostcodeTech\Exceptions\PostcodeNotFoundException;
use Hpolthof\PostcodeTech\Exceptions\ValidationException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @internal
 */
final class Client
{
    public function __construct(
        private readonly string $token,
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @return array{
     *     street: string,
     *     city: string,
     * }
     *
     * @throws PostcodeNotFoundException
     * @throws ValidationException
     * @throws HttpException
     */
    public function get(string $postcode, int $number): array
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                'https://postcode.tech/api/v1/postcode',
                [
                    'auth_bearer' => $this->token,
                    'query' => [
                        'postcode' => $postcode,
                        'number' => $number,
                    ],
                ],
            );

            $statusCode = $response->getStatusCode();

            $responseBody = $response->getContent(false);
        } catch (TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $exception) {
            throw new HttpException($exception->getMessage(), previous: $exception);
        }

        if (200 > $statusCode || 300 <= $statusCode) {
            throw match ($statusCode) {
                401 => new HttpException('Unauthorized', $responseBody),
                404 => new PostcodeNotFoundException('No results found for the given postcode and number.', $responseBody),
                422 => new ValidationException('The request data was invalid.', $responseBody),
                default => new HttpException('An unknown error occurred while fetching the data from the API.', $responseBody),
            };
        }

        try {
            $array = json_decode($responseBody, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new HttpException('The response body is not a valid JSON array.', $responseBody, $exception);
        }

        if (!is_array($array) || empty($array['street']) || !is_string($array['street']) || empty($array['city']) || !is_string($array['city'])) {
            throw new HttpException('The response body did not contain the expected data.', $responseBody);
        }

        return $array;
    }
}
