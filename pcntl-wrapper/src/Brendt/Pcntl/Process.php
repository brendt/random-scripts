<?php

namespace Brendt\Pcntl;

/**
 * A Process (class) must implement the `execute` method, which will be called from within a forked process (system).
 *
 * Class Process
 * @package Brendt\Pcntl
 */
abstract class Process
{
    /**
     * @var string
     */
    protected $pid;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var resource
     */
    protected $socket;

    /**
     * @var callable
     */
    protected $successCallback;

    /**
     * @var int Time when the process started.
     */
    protected $startTime;

    /**
     * @var int Maximum runtime in seconds.
     */
    protected $maxRunTime = 300;

    /**
     * Execute the process.
     *
     * @return mixed
     */
    public abstract function execute();

    /**
     * Register a callback for when this process successfully finishes.
     *
     * @param callable $callback
     *
     * @return Process
     */
    public function onSuccess(callable $callback) : Process {
        $this->successCallback = $callback;

        return $this;
    }

    /**
     * Trigger the success callback.
     *
     * @return mixed|null
     *
     * @see \Brendt\Pcntl\AsyncManager::wait()
     */
    public function triggerSuccess() {
        if (!$this->successCallback) {
            return null;
        }

        return call_user_func_array($this->successCallback, [$this]);
    }

    public function setPid($pid) : Process {
        $this->pid = $pid;

        return $this;
    }

    public function getPid() {
        return $this->pid;
    }

    public function setSocket($socket) : Process {
        $this->socket = $socket;

        return $this;
    }

    public function getSocket() {
        return $this->socket;
    }

    public function setName(string $name) : Process {
        $this->name = $name;

        return $this;
    }

    public function getName() : string {
        return $this->name;
    }

    public function setStartTime($startTime) {
        $this->startTime = $startTime;

        return $this;
    }

    public function getStartTime() {
        return $this->startTime;
    }

    public function setMaxRunTime(int $maxRunTime) : Process {
        $this->maxRunTime = $maxRunTime;

        return $this;
    }

    public function getMaxRunTime() : int {
        return $this->maxRunTime;
    }
}
