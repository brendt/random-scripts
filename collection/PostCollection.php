<?php

class Post {}

class PostCollection extends Collection
{
    public function current() : ?Post {
        return parent::current();
    }

    public function offsetGet($offset) : ?Post{
        return parent::offsetGet($offset);
    }

    public function offsetSet($offset, $value) {
        if (!$value instanceof Post) {
            throw new InvalidArgumentException("value must be instance of Post.");
        }

        parent::offsetSet($offset, $value);
    }
}
