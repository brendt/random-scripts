<?php

namespace Http\Uri;

use Http\Uri;
use PHPUnit\Framework\TestCase;

class FragmentTest extends TestCase
{
    /** @test */
    public function fragment_can_be_empty() {
        $uri = new Uri('https://stitcher.io');

        $this->assertEquals('', $uri->getFragment());
    }

    /** @test */
    public function fragment_hashbang_is_not_part_of_the_fragment_string() {
        $uri = new Uri('https://stitcher.io#fragment');

        $this->assertNotContains('#', $uri->getFragment());
    }

    /** @test */
    public function fragment_must_be_percent_encoded() {
        $uri = new Uri('https://stitcher.io#%');

        $this->assertEquals('%25', $uri->getFragment());
    }

    /** @test */
    public function fragment_must_not_be_double_percent_encoded() {
        $uri = new Uri('https://stitcher.io#%25');

        $this->assertEquals('%25', $uri->getFragment());
    }

    /** @test */
    public function with_fragment_must_retain_state() {
        $uri = new Uri('https://stitcher.io');
        $new = $uri->withFragment('fragment');

        $this->assertNotEquals($uri, $new);
    }

    /** @test */
    public function with_fragment_can_be_empty() {
        $uri = new Uri('https://stitcher.io#fragment');
        $new = $uri->withFragment('');

        $this->assertEquals('', $new->getFragment());
    }

    /** @test */
    public function with_fragment_must_be_percent_encoded() {
        $uri = new Uri('https://stitcher.io');
        $new = $uri->withFragment('%');

        $this->assertEquals('%25', $new->getFragment());
    }

    /** @test */
    public function with_fragment_must_not_be_double_percent_encoded() {
        $uri = new Uri('https://stitcher.io');
        $new = $uri->withFragment('%25');

        $this->assertEquals('%25', $new->getFragment());
    }
}
