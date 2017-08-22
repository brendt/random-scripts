<?php

namespace Http\Uri;

use Http\Uri;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    /** @test */
    public function path_can_be_empty() {
        $uri = new Uri('');

        $this->assertEquals('', $uri->getPath());
    }

    /** @test */
    public function path_can_be_root() {
        $uri = new Uri('/root');

        $this->assertEquals('/root', $uri->getPath());
    }

    /** @test */
    public function path_can_be_rootless() {
        $uri = new Uri('root');

        $this->assertEquals('root', $uri->getPath());
    }

    /** @test */
    public function root_paths_and_empty_paths_are_not_normalised() {
        $rootUri = new Uri('/');
        $emptyUri = new Uri('');

        $this->assertEquals('/', $rootUri->getPath());
        $this->assertEquals('', $emptyUri->getPath());
    }

    /** @test */
    public function path_must_be_percent_encoded() {
        $uri = new Uri('/hello%world');

        $this->assertEquals('/hello%25world', $uri->getPath());
    }

    /** @test */
    public function path_percent_encoded_values_are_skipped() {
        $uri = new Uri('/hello%25world');

        $this->assertEquals('/hello%25world', $uri->getPath());
    }

    /** @test */
    public function with_path_must_retain_state() {
        $uri = new Uri('https://stitcher.io');
        $new = $uri->withPath('/path');

        $this->assertNotEquals($uri, $new);
    }

    /** @test */
    public function with_path_supports_empty_value() {
        $uri = new Uri('https://stitcher.io');
        $new = $uri->withPath('');

        $this->assertEquals('', $new->getPath());
    }

    /** @test */
    public function with_path_supports_root_path() {
        $uri = new Uri('https://stitcher.io');
        $new = $uri->withPath('/');

        $this->assertEquals('/', $new->getPath());
    }

    /** @test */
    public function with_path_supports_rootless_path() {
        $uri = new Uri('https://stitcher.io');
        $new = $uri->withPath('path');

        $this->assertEquals('path', $new->getPath());
    }

    /** @test */
    public function with_path_supports_encoding() {
        $uri = new Uri('https://stitcher.io');
        $new = $uri->withPath('/%');

        $this->assertEquals('/%25', $new->getPath());
    }

    /** @test */
    public function with_path_supports_already_encoded_values() {
        $uri = new Uri('https://stitcher.io');
        $new = $uri->withPath('/%25');

        $this->assertEquals('/%25', $new->getPath());
    }
}
