<?php

namespace Http\Uri;

use Http\Uri;
use PHPUnit\Framework\TestCase;

class PortTest extends TestCase
{
    /** @test */
    public function standard_port_must_return_null() {
        $uri = new Uri('https://stitcher.io:443');

        $this->assertNull($uri->getPort());
    }

    /** @test */
    public function non_standard_port_must_return_port() {
        $uri = new Uri('https://stitcher.io:80');

        $this->assertEquals(80, $uri->getPort());
    }

    /** @test */
    public function port_must_be_integer() {
        $uri = new Uri('https://stitcher.io:80');

        $this->assertTrue(is_int($uri->getPort()));
    }

    /** @test */
    public function no_port_must_return_null() {
        $uri = new Uri('https://stitcher.io');

        $this->assertNull($uri->getPort());
    }

    /** @test */
    public function with_port_must_retain_state() {
        $uri = new Uri('https://stitcher.io');
        $new = $uri->withPort(80);

        $this->assertNotEquals($uri, $new);
    }

    /** @test */
    public function with_port_must_be_null_if_standard_port() {
        $uri = new Uri('https://stitcher.io');
        $new = $uri->withPort(443);

        $this->assertNull($new->getPort());
    }

    /** @test */
    public function with_port_supports_empty_value() {
        $uri = new Uri('https://stitcher.io:80');
        $new = $uri->withPort(null);

        $this->assertNull($new->getPort());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function invalid_port_below_1_throws_exception() {
        $uri = new Uri('https://stitcher.io');
        $uri->withPort(0);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function invalid_port_above_65535_throws_exception() {
        $uri = new Uri('https://stitcher.io');
        $uri->withPort(65536);
    }
}
