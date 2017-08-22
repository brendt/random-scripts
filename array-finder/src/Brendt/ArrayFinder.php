<?php

namespace Brendt;

class ArrayFinder implements \ArrayAccess, \Iterator
{
    const TOKEN_NESTING = '.';
    const TOKEN_REPETITION = '*';

    private $array = [];
    private $position = 0;

    public function __construct(array $array = []) {
        $this->array = $array;
    }

    public static function fromArray(array $array): ArrayFinder {
        return new self($array);
    }

    public function get($pathParts, array $element = null) {
        if (!is_array($pathParts)) {
            $pathParts = explode(self::TOKEN_NESTING, $pathParts);
        }

        if (!$element) {
            $element = $this->array;
        }

        foreach ($pathParts as $key => $pathPart) {
            if ($pathPart === self::TOKEN_REPETITION && is_array($element)) {
                $result = new self();
                $partialPath = array_splice($pathParts, $key + 1);

                foreach ($element as $items) {
                    $result[] = $this->get($partialPath, $items);
                }

                return $result;
            }

            if (!isset($element[$pathPart])) {
                return null;
            }

            $element = $element[$pathPart];
        }

        return $element;
    }

    public function set($pathParts, $value, &$element = null, array $callbackArguments = []): ArrayFinder {
        if (!is_array($pathParts)) {
            $pathParts = explode(self::TOKEN_NESTING, $pathParts);
        }

        end($pathParts);
        $lastPathKey = key($pathParts);
        reset($pathParts);

        if (!$element) {
            $element = &$this->array;
        }

        foreach ($pathParts as $key => $pathPart) {
            if ($pathPart === self::TOKEN_REPETITION && is_array($element)) {
                $partialPath = array_splice($pathParts, $key + 1);

                foreach ($element as $itemKey => &$item) {
                    $this->set($partialPath, $value, $item, [new self($item), $itemKey]);
                }

                return $this;
            }

            if (!isset($element[$pathPart])) {
                $element[$pathPart] = $key === $lastPathKey ? null : [];
            }

            $element = &$element[$pathPart];
        }

        if ($value instanceof \Closure) {
            $element = call_user_func_array($value, $callbackArguments);
        } else {
            $element = $value;
        }

        unset($element);

        return $this;
    }

    public function toArray(): array {
        $array = $this->array;
        reset($array);

        return $array;
    }

    public function current() {
        return current($this->array);
    }

    public function offsetGet($offset) {
        return isset($this->array[$offset]) ? $this->array[$offset] : $this->get($offset);
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->array[] = $value;
        } elseif (isset($this->array[$offset])) {
            $this->array[$offset] = $value;
        } else {
            $this->set($offset, $value);
        }
    }

    public function offsetExists($offset) {
        return isset($this->array[$offset]) || $this->get($offset) !== null;
    }

    public function offsetUnset($offset) {
        unset($this->array[$offset]);
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
}
