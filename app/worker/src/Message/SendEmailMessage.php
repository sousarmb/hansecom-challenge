<?php

namespace App\Message;

use Symfony\Component\Mime\Address;

final class SendEmailMessage
{
    public function __construct(
        private readonly string $to,
        private readonly Address $from,
        private readonly string $subject,
        private readonly string $content,
        private readonly bool $textOrHtml,
    ) {}

    public function getTo(): string {
        return $this->to;
    }

    public function getFrom(): Address {
        return $this->from;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function getTextOrHtml(): bool {
        return $this->textOrHtml;
    }
}
