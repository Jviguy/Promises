<?php

namespace Jviguy\Promises;

use pocketmine\Server;
use pocketmine\snooze\SleeperNotifier;

class Promise
{

    private PromiseThread $thread;

    /**
     * Creates a new promise that
     *
     * @param callable $fn - the callable to be run on another thread.
     */
    public function __construct(callable $fn) {
        $notifier = new SleeperNotifier();
        Server::getInstance()->getTickSleeper()->addNotifier($notifier, function() : void{
            $this->handle();
        });
        $this->thread = new PromiseThread($fn, $notifier);
    }

    /** @var callable[] $callstack - the callbacks to be called onCompletion */
    private array $callstack = [];

    /** @var callable[] $catchers - the given callbacks that are catching errors */
    private array $catchers = [];

    /** @var callable[] $finals - the callbacks that will run no matter the result */
    private array $finals = [];

    /**
     * Adds a new successful callback to this promise
     *
     * @param callable $fn - the given callback to be run when this promise is successfully resolved
     */
    public function then(callable $fn) {
        $this->callstack[] = $fn;
    }

    /**
     * @param callable $fn
     * Runs after the promise returns, no matter the result
     */
    public function finally(callable $fn){
        $this->finals[] = $fn;
    }


    /**
     * Adds a new refused callback to this promise
     *
     * @param callable $fn - the given callback to be run when this promise fails to resolve.
     */
    public function catch(callable $fn) {
    	$this->catchers[] = $fn;
	}

    /**
     * called when the given thread is finished running should never be called on the calling thread.
     *
     * @internal
     */
    public function handle() {
        if ($this->thread->errored) {
            foreach ($this->catchers as $catcher) {
                $catcher($this->thread->error);
            }
        } else {
            // Promise didn't error
            foreach ($this->callstack as $c) {
                $c($this->thread->ret);
            }
        }
        foreach($this->finals as $f){
            $f($this->thread->ret, $this->thread->error);
        }
    }
}