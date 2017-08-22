<?php

trait CreateStatic
{
    public static function create(...$args) {
        return new self(...$args);
    }
}

