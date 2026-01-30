<?php

namespace Kkevindev\PostcodeTech;

use Kkevindev\PostcodeTech\Exceptions\HttpException;
use Kkevindev\PostcodeTech\Exceptions\PostcodeNotFoundException;
use Kkevindev\PostcodeTech\Exceptions\ValidationException;
use Symfony\Component\HttpClient\HttpClient;

class Postcode implements PostcodeInterface
{
    protected function __construct(
        protected string $postcode,
        protected int $number,
        protected string $street,
        protected string $city,
    ) {
    }

    /**
     * @throws HttpException
     * @throws ValidationException
     * @throws PostcodeNotFoundException
     */
    public static function search(string $postcode, int $number, string $token): self
    {
        $client = new Client($token, HttpClient::create());

        $response = $client->get($postcode, $number);

        return new self(
            $postcode,
            $number,
            $response['street'],
            $response['city'],
        );
    }

    public function postcode(): string
    {
        return $this->postcode;
    }

    public function number(): int
    {
        return $this->number;
    }

    public function street(): string
    {
        return $this->street;
    }

    public function city(): string
    {
        return $this->city;
    }
}
