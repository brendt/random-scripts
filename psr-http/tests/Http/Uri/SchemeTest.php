<?php

namespace Http\Uri;

use Http\Uri;
use PHPUnit\Framework\TestCase;

class SchemeTest extends TestCase
{
    /** @test */
    public function scheme_returned() {
        $uri = new Uri('https://www.stitcher.io');

        $this->assertEquals('https', $uri->getScheme());
    }

    /** @test */
    public function no_scheme_must_return_an_empty_string() {
        $uri = new Uri('www.stitcher.io');

        $this->assertEquals('', $uri->getScheme());
    }

    /** @test */
    public function scheme_must_be_normalised_to_lowercase() {
        $uri = new Uri('HTTPS://www.stitcher.io');

        $this->assertEquals('https', $uri->getScheme());
    }

    /** @test */
    public function trailing_colon_must_not_be_added() {
        $uri = new Uri('https://www.stitcher.io');

        $this->assertNotEquals(':', $uri->getScheme());
    }

    /** @test */
    public function with_scheme_must_retain_state() {
        $uri = new Uri('http://www.stitcher.io');
        $new = $uri->withScheme('https');

        $this->assertNotEquals($uri, $new);
    }

    /** @test */
    public function with_scheme_sets_scheme() {
        $uri = new Uri('http://www.stitcher.io');
        $new = $uri->withScheme('https');

        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('https', $new->getScheme());
    }

    /** @test */
    public function with_scheme_normalises_the_value() {
        $uri = new Uri('http://www.stitcher.io');
        $new = $uri->withScheme('HTTPS');

        $this->assertEquals('https', $new->getScheme());
    }

    /** @test */
    public function with_scheme_supports_empty_value() {
        $uri = new Uri('http://www.stitcher.io');
        $new = $uri->withScheme('');

        $this->assertNotContains('http', (string) $new);
    }
}
