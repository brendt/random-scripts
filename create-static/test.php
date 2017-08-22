<?php

require_once __DIR__ . '/CreateStatic.php';

class Foo {
    use CreateStatic;

    private $foo;
    private $bar;

    public function __construct(int $foo, string $bar) {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}

$foo = Foo::create(25, 'bar');
var_dump($foo);
