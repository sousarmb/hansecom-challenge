<?php

namespace App\Helpers;

use App\Models\GlobalQuote;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * This client only handles global quote requests
 */
class AlphaVantageClient extends Client {

    private Response $lastResponse;
    private GlobalQuote $lastQuote;

    public function __construct(?string $apiHostFQDN = null)
    {
        parent::__construct([
            'base_uri' => $apiHost ?? $_ENV['ALPHAVANTAGE_API_URL'],
            'timeout'  => 2.0,
            'headers' => [
                'Accept' => "application/json",
            ]
        ]);
    }

    /**
     * Get quote for $symbol
     * 
     * @param string $symbol
     * @throws RuntimeException When response status code other than 200 or response content type other than JSON
     * @return GlobalQuote 
     */
    public function getQuote(string $symbol): GlobalQuote {
        $query = http_build_query([
            'function' => 'GLOBAL_QUOTE',
            'datatype' => 'json',
            'symbol' => $symbol,
            'apikey' => $_ENV['ALPHAVANTAGE_API_KEY']
        ]);
        $this->lastResponse = $this->get("/query?$query");
        if ($this->lastResponse->getStatusCode() !== 200) {
            throw new \RuntimeException("Remote server response with status code: " . $this->lastResponse->getStatusCode());
        }
        if ($this->lastResponse->getHeader("Content-Type")[0] != 'application/json') {
            throw new \RuntimeException("Expecting JSON response but got: " . $this->lastResponse->getHeader("Content-Type")[0]);
        }

        $this->lastQuote = new GlobalQuote($this->lastResponse->getBody()->getContents());
        $this->lastResponse->getBody()->rewind();

        return $this->lastQuote;
    }

    public function getLastQuote(): GlobalQuote|null {
        return $this->lastQuote instanceof GlobalQuote ? $this->lastQuote : null;
    }

    public function getLastResponse(): Response|null {
        return $this->lastResponse instanceof Response ? $this->lastResponse : null;
    }
}