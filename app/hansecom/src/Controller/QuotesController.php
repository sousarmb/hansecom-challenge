<?php

namespace App\Controller;

use App\Message\SendEmailMessage;
use App\Services\QuotesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use RuntimeException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class QuotesController extends AbstractController
{
    #[Route('/quotes', name: 'get_quote_requests', methods:['GET'])]
    public function index(
        Request $request,
        QuotesService $quotesService
    ): Response
    {
        try {
            $quotes = $quotesService->getQuoteRequests($request->get('offset') ?? 1);
            
            return new Response(
                $quotes,
                Response::HTTP_OK,
                ['Content-Type' => 'application/json']
            );
        } catch (\RuntimeException $e) {
            return new Response(
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/quote', name: 'request_quote', methods:['GET'])]
    public function requestQuote(
        Request $request,
        QuotesService $quotesService,
        MessageBusInterface $bus,
        TranslatorInterface $translator,
        Environment $twig
    ): Response {
        $symbol = $request->get('symbol');
        if (!$quotesService->validateSymbol($symbol)) {
            return new Response(
                '',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $quote = $quotesService->requestQuote($symbol);
/*
$quote = (object)[
    "owner"=> "cc232d75ddc497a622c74448025aa1ec77d1e55aaafafed738a7d8a2e4fa2cc2",
    "datetime_request"=> "2025-03-28 17:32:45",
    "quote" => [
        "symbol"=> "AAPL",
        "open"=> 221.39,
        "high"=> 224.99,
        "low"=> 220.5601,
        "price"=> 223.85,
        "volume"=> 37094774,
        "latest_trading_day"=> "2025-03-27",
        "previous_close"=> 221.53,
        "change"=> 2.32,
        "change_percent"=> 1.0473,
    ],
    "symbol" => "aapl"
];
*/
            $emailMessage = new SendEmailMessage(
                $this->getUser()->getEmail(),
                (new Address($_ENV['HANSECOM_QUOTE_REQUEST_EMAIL'], $_ENV['HANSECOM_EMAIL_NAME'])),
                $translator->trans('quote_request') . ": $symbol",
                $twig->render('quotes/quote_request_email.html.twig', [
                    'symbol' => $symbol,
                    'quote' => $quote
                ]),
                true
            );

dump($emailMessage);

            $bus->dispatch($emailMessage);
            
            return new Response(
                json_encode($quote),
                Response::HTTP_OK,
                ['Content-Type' => 'application/json']
            );
        } catch (\RuntimeException $e) {
            return new Response(
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new Response(
            '',
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
