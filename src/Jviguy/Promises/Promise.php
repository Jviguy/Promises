<?php

namespace Jviguy\Promises;

use pocketmine\Server;
use pocketmine\snooze\SleeperNotifier;

class Promise
{

    private PromiseThread $thread;

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

    public function then(callable $fn) {
        $this->callstack[] = $fn;
    }

    public function catch(callable $fn) {
    	$this->catchers[] = $fn;
	}

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
    }
}