<?php

namespace Http\Uri;

use Http\Uri;
use PHPUnit\Framework\TestCase;

class ToStringTest extends TestCase
{
    /** @test */
    public function schema_is_appended_by_colon() {
        $uri = (new Uri())->withScheme('https');

        $this->assertEquals('https:', (string) $uri);
    }
    /** @test */
    public function authority_is_prepended_by_double_slash() {
        $uri = (new Uri())->withHost('stitcher.io');

        $this->assertEquals('//stitcher.io', (string) $uri);
    }

    /** @test */
    public function rootless_path_is_prepended_with_slash_if_authority_isset() {
        $uri = (new Uri())->withHost('stitcher.io')->withPath('rootless');

        $this->assertEquals('//stitcher.io/rootless', (string) $uri);
    }

    /** @test */
    public function rootless_path_is_no_prepended_with_slash_if_authority_not_isset() {
        $uri = (new Uri())->withPath('rootless');

        $this->assertEquals('rootless', (string) $uri);
    }

    /** @test */
    public function root_path_is_not_prepended_with_slash_if_authority_isset() {
        $uri = (new Uri())->withHost('stitcher.io')->withPath('/root');

        $this->assertEquals('//stitcher.io/root', (string) $uri);
    }

    /** @test */
    public function root_path_is_keeps_leading_slash_if_authority_not_isset() {
        $uri = (new Uri())->withPath('/root');

        $this->assertEquals('/root', (string) $uri);
    }

    /** @test */
    public function root_path_with_multiple_slashes_is_kept_if_authority_isset() {
        $uri = (new Uri())->withHost('stitcher.io')->withPath('///root');

        $this->assertEquals('//stitcher.io///root', (string) $uri);
    }

    /** @test */
    public function root_path_with_multiple_slashes_is_reduced_to_one_slash_if_authority_not_isset() {
        $uri = (new Uri())->withPath('///root');

        $this->assertEquals('/root', (string) $uri);
    }

    /** @test */
    public function query_is_prefixed_with_a_question_mark() {
        $uri = (new Uri())->withQuery('a=a');

        $this->assertEquals('?a=a', (string) $uri);
    }

    /** @test */
    public function fragment_is_prefixed_with_a_hashbang() {
        $uri = (new Uri())->withFragment('fragment');

        $this->assertEquals('#fragment', (string) $uri);
    }
}
