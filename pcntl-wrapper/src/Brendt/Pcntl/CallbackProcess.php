<?php

namespace Brendt\Pcntl;

/**
 * The CallbackProcess provides a simple process with one callback to be executed in that process.
 * Useful for simpler processes.
 *
 * Class CallbackProcess
 * @package Brendt\Pcntl
 */
class CallbackProcess extends Process
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * CallbackProcess constructor.
     *
     * @param callable|null $callback
     */
    public function __construct(callable $callback = null) {
        $this->callback = $callback;
    }

    /**
     * Executes the set callback.
     *
     * @return mixed|null
     */
    public function execute() {
        if (!$this->callback) {
            return null;
        }

        return call_user_func_array($this->callback, [$this]);
    }
}
