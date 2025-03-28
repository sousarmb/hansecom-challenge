<?php

namespace App\Models;

use JsonSerializable;

class GlobalQuote implements JsonSerializable {
   
    private string $symbol; // IBM
    private float $open; // 248.3600
    private float $high; // 250.9000
    private float $low; // 248.2000
    private float $price; // 249.9000
    private int $volume; // 3133809
    private string $latestTradingDay; // 2025-03-25
    private float $previousClose; // 248.4500
    private float $change; // 1.4500
    private float $changePercent; // 0.5836

    public function getSymbol(): string {
        return $this->symbol;
    }

    public function getOpen(): float {
        return $this->open;
    }

    public function getHigh(): float {
        return $this->high;
    }

    public function getLow(): float {
        return $this->low;
    }

    public function getPrice(): float {
        return $this->price;
    }
    
    public function getVolume(): int {
        return $this->volume;
    }

    public function getLatestTradingDay(): string {
        return $this->latestTradingDay;
    }

    public function getPreviousClose(): float {
        return $this->previousClose;
    }

    public function getChange(): float {
        return $this->change;
    }

    public function getChangePercent(): float {
        return $this->changePercent;
    }

    public function jsonSerialize(): array {
        return [
            'symbol' => $this->symbol,
            'open' => $this->open,
            'high' => $this->high,
            'low' => $this->low,
            'price' => $this->price,
            'volume' => $this->volume,
            'latest_trading_day' => $this->latestTradingDay,
            'previous_close' => $this->previousClose,
            'change' => $this->change,
            'change_percent' => $this->changePercent
        ];
    }

    /**
     * @throws RuntimeException When quote property does not map to model property
     */
    public function __construct(object|string $quote) {
        if (is_string($quote)) {
            $quote = json_decode($quote, false);
        }

        foreach($quote->{"Global Quote"} as $k => $v) {
            switch(explode(". ", $k)[1]) {
                case 'symbol': 
                    $this->symbol = $v;
                    break;
                case 'open': 
                    $this->open = floatval($v);
                    break;
                case 'high':
                    $this->high = floatval($v);
                    break;
                case 'low':
                    $this->low = floatval($v);
                    break;
                case 'price':
                    $this->price = floatval($v);
                    break;
                case 'volume':
                    $this->volume = intval($v);
                    break;
                case 'latest trading day':
                    $this->latestTradingDay = $v; // could convert to DateTimeImmutable but that would give the time as well, which we don't want
                    break;
                case 'previous close':
                    $this->previousClose = floatval($v);
                    break;
                case 'change':
                    $this->change = floatval($v);
                    break;
                case 'change percent':
                    $this->changePercent = floatval($v);
                    break;
                default:
                    throw new \RuntimeException('Invalid quote property:' . $k);
                    break;
            }
        }
    }
}
