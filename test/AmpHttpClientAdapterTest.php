<?php declare(strict_types=1);

use Amp\Http\Client\HttpClient;
use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\Psr7\PsrAdapter;
use Amp\PHPUnit\AsyncTestCase;
use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\ResponseFactory;
use Nessworthy\AmpHttpPlug\AmpHttpClientAdapter;
use Psr\Http\Message\ResponseInterface;

class AmpHttpClientAdapterTest extends AsyncTestCase
{
    public function testAsync()
    {
        $httpClient = HttpClientBuilder::buildDefault();
        $rf = new RequestFactory();
        $psrAdapter = new PsrAdapter(
            $rf,
            new ResponseFactory()
        );

        $adapter = new AmpHttpClientAdapter(
            $httpClient,
            $psrAdapter
        );


        $req = $rf->createRequest('GET', 'https://nessworthy.me');

        $promise = $adapter->sendAsyncRequest($req);

        /** @var ResponseInterface $response */
        $response = $promise->wait(true);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSync()
    {
        $httpClient = HttpClientBuilder::buildDefault();
        $rf = new RequestFactory();
        $psrAdapter = new PsrAdapter(
            $rf,
            new ResponseFactory()
        );

        $adapter = new AmpHttpClientAdapter(
            $httpClient,
            $psrAdapter
        );


        $req = $rf->createRequest('GET', 'https://nessworthy.me');

        $response = $adapter->sendRequest($req);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
