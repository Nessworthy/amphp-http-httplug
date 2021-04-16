<?php declare(strict_types=1);

namespace Nessworthy\AmpHttpPlug;

use Amp\ByteStream\InputStream;
use Amp\Promise;
use Amp\Success;
use Psr\Http\Message\StreamInterface;

class AmpPsrMessageStream implements InputStream
{
    public const DEFAULT_CHUNK_SIZE = 8192;

    /**
     * @var StreamInterface
     */
    private $stream;
    /**
     * @var int
     */
    private $chunkSize;

    public function __construct(StreamInterface $stream, int $chunkSize = self::DEFAULT_CHUNK_SIZE)
    {
        $this->stream = $stream;
        $this->chunkSize = $chunkSize;
    }

    public function read(): Promise
    {
        $contents = '';
        while (!$this->stream->eof()) {
            $contents .= $this->stream->read(self::DEFAULT_CHUNK_SIZE);
            return new Success($contents);
        }
    }

}
