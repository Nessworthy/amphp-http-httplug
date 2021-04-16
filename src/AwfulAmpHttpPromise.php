<?php declare(strict_types=1);

namespace Nessworthy\AmpHttpPlug;

use Amp\Loop;
use Http\Promise\Promise;
use Throwable;
use function Amp\call;

class AwfulAmpHttpPromise implements Promise
{
    private $promise;
    private $chain = [];
    private $state = self::PENDING;

    public function __construct(callable $callable)
    {
        $this->promise = call($callable);
    }

    public function then(callable $onFulfilled = null, callable $onRejected = null)
    {
        $this->chain[] = [$onFulfilled, $onRejected];
        return $this;
    }

    public function getState()
    {
        return $this->state;
    }

    public function wait($unwrap = true)
    {
        $return = null;

        Loop::run(function() use (&$return, $unwrap) {

            try {
                $current = yield $this->promise;
            } catch (Throwable $throwable) {
                $this->state = self::REJECTED;
                if (isset($this->chain[0], $this->chain[0][1])) {
                    $this->chain[0][1]($throwable);
                }
                if ($unwrap) {
                    throw $throwable;
                }
                return;
            }

            foreach ($this->chain as $i => [$onSuccess, $onError]) {
                try {
                    $current = $onSuccess($current);
                } catch (Throwable $throwable) {
                    $this->state = self::REJECTED;
                    if (isset($this->chain[$i+1], $this->chain[$i+1][1])) {
                        $this->chain[$i+1][1]($throwable);
                    }
                    if ($unwrap) {
                        throw $throwable;
                    }
                    return;
                }
            }
            $this->state = self::FULFILLED;
            if ($unwrap) {
                $return = $current;
            }
        });

        return $return;
    }

}
