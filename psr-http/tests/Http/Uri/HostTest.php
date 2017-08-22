<?php

namespace Http\Uri;

use Http\Uri;
use PHPUnit\Framework\TestCase;

class HostTest extends TestCase
{
    /** @test */
    public function no_host_must_return_empty_string() {
        $uri = new Uri('/path');

        $this->assertEquals('', $uri->getHost());
    }

    /** @test */
    public function host_must_be_normalised_to_lowercase() {
        $uri = new Uri('https://STITCHER.IO');

        $this->assertEquals('stitcher.io', $uri->getHost());
    }

    /** @test */
    public function host_returned() {
        $uri = new Uri('https://stitcher.io');

        $this->assertEquals('stitcher.io', $uri->getHost());
    }

    /** @test */
    public function with_host_must_retain_state() {
        $uri = new Uri('http://stitcher.io');
        $new = $uri->withHost('www.stitcher.io');

        $this->assertNotEquals($uri, $new);
    }

    /** @test */
    public function with_host_sets_hosts() {
        $uri = new Uri('http://stitcher.io');
        $new = $uri->withHost('www.stitcher.io');

        $this->assertEquals('stitcher.io', $uri->getHost());
        $this->assertEquals('www.stitcher.io', $new->getHost());
    }

    /** @test */
    public function with_host_supports_empty_value() {
        $uri = new Uri('https://stitcher.io');
        $new = $uri->withHost('');

        $this->assertEquals('', $new->getHost());
    }
}
