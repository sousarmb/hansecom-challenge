<?php

namespace App\MessageHandler;

use App\Message\SendEmailMessage;
use Symfony\Component\Mime\Email;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mailer\MailerInterface;

#[AsMessageHandler]
final class SendEmailMessageHandler
{
    public function __construct(
        private MailerInterface $mailer
    ) {}

    public function __invoke(
        SendEmailMessage $message
    ): void
    {
        $email = (new Email())
            ->from($message->getFrom())
            ->to($message->getTo())
            ->subject($message->getSubject());
        if ($message->getTextOrHtml()) {
            $email->html($message->getContent());
        } else {
            $email->text($message->getContent());
        }

        $this->mailer->send($email);
    }
}
