<?php declare(strict_types=1);

namespace Nessworthy\AmpHttpPlug;

use Amp\Http\Client\HttpClient as AmpHttpClient;
use Amp\Http\Client\Psr7\PsrAdapter;
use Amp\Http\Client\Response;
use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AmpHttpClientAdapter implements HttpAsyncClient, HttpClient
{
    /**
     * @var HttpClient
     */
    private $httpClient;
    /**
     * @var PsrAdapter
     */
    private $psrAdapter;

    public function __construct(AmpHttpClient $httpClient, PsrAdapter $psrAdapter)
    {
        $this->httpClient = $httpClient;
        $this->psrAdapter = $psrAdapter;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $promise = new AwfulAmpHttpPromise(function() use ($request) {
            /** @var Response $response */
            $response = yield $this->httpClient->request($this->psrAdapter->fromPsrRequest($request));
            return $this->psrAdapter->toPsrResponse($response);
        });
        return $promise->wait();
    }

    public function sendAsyncRequest(RequestInterface $request)
    {
        return new AwfulAmpHttpPromise(function() use ($request) {
            /** @var Response $response */
            $response = yield $this->httpClient->request($this->psrAdapter->fromPsrRequest($request));
            return $this->psrAdapter->toPsrResponse($response);
        });
    }

}
