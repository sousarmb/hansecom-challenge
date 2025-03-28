<?php

namespace App\Services;

use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class QuotesService {

    public function __construct(
        private HttpClientInterface $client,
        private Security $security,
        private ValidatorInterface $validator
    ) {}

    /**
     * Get all quote requests this user has done, as JSON encoded string
     * 
     * @param   int $offset Offset to get requests from
     * @return  string
     */
    public function getQuoteRequests(int $offset): string {
        $query = http_build_query([
            'offset' => $offset,
            'owner' => hash('sha256', $this->security->getUser()->getEmail())
        ]);
        $response = $this->client->request('GET', "http://quotes/quotes?$query", [
            'headers' => [
                'Accept' => 'application/json'
            ],
        ]);
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new RuntimeException('Quotes service response status code: ' . $response->getStatusCode());
        }

        return $response->getContent();
    }

    /**
     * Request one quote for specific symbol
     * 
     * @param   string  $symbol Get quote for that symbol (Example: IBM, MSFT, ...)
     * @return  null|object
     */
    public function requestQuote(string $symbol): null | object {
        $response = $this->client->request('POST', 'http://quotes/quote', [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'body' => [
                'symbol' => $symbol,
                'owner' => hash('sha256', $this->security->getUser()->getEmail()),
                'datetime_request' => time()
            ],
        ]);
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new RuntimeException('Quotes service response status code: ' . $response->getStatusCode());
        }

        return json_decode($response->getContent(), false);
    }

    /**
     * Validates symbol sent by the client
     * 
     * @return bool true if symbol is considered valid, false otherwise
     */
    public function validateSymbol(string $symbol): bool {
        $constraints = new Assert\Collection([
            'symbol' => [
                new Assert\Type(['type' => 'string']),
                new Assert\NotBlank(),
                new Assert\Length([
                    'min' => 3,
                    'max' => 9,
                ]),
                new Assert\Regex(['pattern' => '/^[a-z0-9.]+$/i'])
            ],
        ]);

        return !(bool)count($this->validator->validate(
            ['symbol' => $symbol],
            $constraints
        ));
    }
}