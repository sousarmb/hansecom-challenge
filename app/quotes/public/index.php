<?php

use App\Helpers\AlphaVantageClient;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

use App\Models\GlobalQuote;
use App\Models\GlobalQuoteLog;
use App\Repositories\GlobalQuoteLogRepository;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
//
// Middleware to check if all necessary environment variables are set
//
$app->add(function (Request $request, RequestHandler $handler) use ($app) {
    $envFileDir = __DIR__ . '/../';
    $envRequiredVarsFile = __DIR__ . '/../required_env_variables.php';
    $envBad = false;
    try {
        $dotenv = Dotenv::createImmutable($envFileDir);
        $dotenv->load();
        
        $required_variables = require_once $envRequiredVarsFile;
        foreach($required_variables as $v) {
            $dotenv->required($v)->notEmpty();
        }
    } catch(InvalidPathException $e) {
        $envBad = !$envBad;
    } catch(RuntimeException $e) {
        $envBad = !$envBad;
    } finally {
        if ($envBad) {
            $response = $app->getResponseFactory()->createResponse();
            $response->getBody()->write($e->getMessage());
            
            return $response->withStatus(500);
        }
    }
    
    return $handler->handle($request);
});

$app->get('/quotes', function (Request $request, Response $response, $args) {

    $repo = new GlobalQuoteLogRepository();
    $dataset = $repo->get(
        $request->getQueryParams()['owner'],
        $request->getQueryParams()['offset']
    );
    $response->getBody()->write(json_encode($dataset));

    return $response->withHeader('Content-Type', 'application/json');;
});

$app->post('/quote', function (Request $request, Response $response, $args) {

    $form = $request->getParsedBody();
    $apiClient = new AlphaVantageClient();

    $repo = new GlobalQuoteLogRepository();
    $log = new GlobalQuoteLog(
        $form['owner'],
        \DateTimeImmutable::createFromTimestamp($form['datetime_request']),
        $apiClient->getQuote($form['symbol']),
        $form['symbol']
    );
    $repo->insert($log);
    $response->getBody()->write(json_encode($log));

    return $response->withHeader('Content-Type', 'application/json');;
});

$app->run();
