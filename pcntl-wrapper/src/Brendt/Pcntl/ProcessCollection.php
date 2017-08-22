<?php

namespace Brendt\Pcntl;

use ArrayAccess;
use InvalidArgumentException;
use Iterator;

/**
 * Wrapper around an array of processes.
 *
 * Class ProcessCollection
 * @package Brendt\Pcntl
 */
class ProcessCollection implements Iterator, ArrayAccess
{
    private $position;

    private $array = [];

    public function __construct() {
        $this->position = 0;
    }

    public function offsetSet($offset, $value) {
        if (!$value instanceof Process) {
            throw new InvalidArgumentException("value must be instance of Process.");
        }

        if (is_null($offset)) {
            $this->array[] = $value;
        } else {
            $this->array[$offset] = $value;
        }
    }

    public function current() : ?Process {
        return $this->array[$this->position];
    }

    public function offsetGet($offset) : ?Process {
        return isset($this->array[$offset]) ? $this->array[$offset] : null;
    }

    public function isEmpty() : bool {
        return count($this->array) === 0;
    }

    public function next() {
        ++$this->position;
    }

    public function key() {
        return $this->position;
    }

    public function valid() {
        return isset($this->array[$this->position]);
    }

    public function rewind() {
        $this->position = 0;
    }

    public function offsetExists($offset) {
        return isset($this->array[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->array[$offset]);
    }

    public function toArray() {
        return $this->array;
    }
}
