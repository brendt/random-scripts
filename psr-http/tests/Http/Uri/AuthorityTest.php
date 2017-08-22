<?php

namespace Http\Uri;

use Http\Uri;
use PHPUnit\Framework\TestCase;

class AuthorityTest extends TestCase
{
    /** @test */
    public function authority_with_host() {
        $uri = new Uri('https://stitcher.io/path');

        $this->assertEquals('stitcher.io', $uri->getAuthority());
    }

    /** @test */
    public function authority_with_user() {
        $uri = new Uri('https://brendt@stitcher.io/path');

        $this->assertEquals('brendt@stitcher.io', $uri->getAuthority());
    }

    /** @test */
    public function authority_with_port() {
        $uri = new Uri('https://stitcher.io:8080/path');

        $this->assertEquals('stitcher.io:8080', $uri->getAuthority());
    }

    /** @test */
    public function default_port_for_scheme_must_not_be_included() {
        $uri = new Uri('http://stitcher.io:80/path');

        $this->assertEquals('stitcher.io', $uri->getAuthority());
    }

    /** @test */
    public function no_authority_must_return_an_empty_string() {
        $uri = new Uri('/path');

        $this->assertEquals('', $uri->getAuthority());
    }
}
