<?php 

namespace App\Models;

use JsonSerializable;

class GlobalQuoteLog implements JsonSerializable{

    private ?int $id = null;
    private string $owner;
    private \DateTimeImmutable $datetime_request;
    private GlobalQuote $quote;
    private string $symbol;

    public function __construct(
        string $owner,
        \DateTimeImmutable $datetime_request,
        GlobalQuote $quote,
        string $symbol
    ) {
        $this->owner = $owner;
        $this->datetime_request = $datetime_request;
        $this->quote = $quote;
        $this->symbol = $symbol;
    }

    public function jsonSerialize(): array
    {
        return [
            'owner' => $this->owner,
            'datetime_request' => $this->datetime_request->format('Y-m-d H:i:s'),
            'quote' => $this->quote,
            'symbol' => $this->symbol
        ];
    }

    /**
     * No ID? Model not persisted!
     */
    public function getId(): ?int {
        return $this->id;
    }

    public function getOwner(): string {
        return $this->owner;
    }

    public function getDateTimeRequest(): \DateTimeImmutable {
        return $this->datetime_request;
    }

    public function getQuote(): GlobalQuote {
        return $this->quote;
    }

    public function getSymbol(): string {
        return $this->symbol;
    }
}