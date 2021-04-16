# AMPHP HTTP to HTTPlug Adapter

Not recognised or endorsed by AMPHP. Would not say it's that good either.

Have a package that requires `php-http/client-implementation`, but you're running AMP? Here's the solution!

## Requirements

* PHP >= 7.2
* An implementation of `psr/http-factory-implementation` and `psr/http-message-implementation`.
  I recommend `laminas/laminas-diactoros` because it has a name that's 100% impossible to remember, and it's a damn good package.
* AMPHP (duh).

## Usage:

```php
// Implementations of request/response factory interfaces. Are not bundled with this package!
use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\ResponseFactory;

use Amp\Http\Client\Psr7\PsrAdapter;
use Nessworthy\AmpHttpPlug\AmpHttpClientAdapter;

use Amp\Http\Client\HttpClientBuilder;

$httpClient = HttpClientBuilder::buildDefault();
$psrAdapter = new PsrAdapter(
    new RequestFactory,
    new ResponseFactory
);

// Implements async and non-async interfaces.
$adapter = new AmpHttpClientAdapter(
    $httpClient,
    $psrAdapter
);
```
