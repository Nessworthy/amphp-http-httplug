<?php declare(strict_types=1);

namespace Nessworthy\AmpHttpPlug;

use Amp\ByteStream\InputStream;
use Amp\Http\Client\RequestBody;
use Amp\Promise;
use Amp\Success;
use Psr\Http\Message\StreamInterface;

class StreamBody implements RequestBody
{
    /**
     * @var StreamInterface
     */
    private $body;
    /**
     * @var array
     */
    private $headers;

    public function __construct(StreamInterface $body, array $headers) {
        $this->body = $body;
        $this->headers = $headers;
    }

    public function getHeaders(): Promise
    {
        return new Success($this->headers);
    }

    public function createBodyStream(): InputStream
    {
        return new AmpPsrMessageStream(clone $this->body);
    }

    public function getBodyLength(): Promise
    {
        return new Success($this->body->getSize() ?: -1);
    }
}
