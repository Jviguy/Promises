<?php

namespace Jviguy\Promises;

use Exception;
use pocketmine\snooze\SleeperNotifier;
use pocketmine\thread\Thread;

class PromiseThread extends Thread
{
    /** @var callable $fn - the callback to be ran. */
    private $fn;

    public bool $errored = false;

    /** @var $ret - the return value of the given $this->fn. */
    public $ret = null;

    /** @var Exception $error - the exception that was thrown if given any hence ($errored) */
    public Exception $error;

    public function __construct(callable $fn, private SleeperNotifier $notifier) {
        $this->fn = $fn;
		$this->start();
    }

    protected function onRun(): void
    {
        try {
            $this->ret = ($this->fn)();
        } catch (Exception $e) {
            $this->errored = true;
            $this->error = $e;
        }
        $this->notifier->wakeupSleeper();
    }
}