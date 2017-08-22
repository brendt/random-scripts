<?php

namespace Http\Uri;

use Http\Uri;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    /** @test */
    public function query_can_be_empty() {
        $uri = new Uri('https://stitcher.io');

        $this->assertEquals('', $uri->getQuery());
    }

    /** @test */
    public function query_question_mark_is_not_part_of_the_query_string() {
        $uri = new Uri('https://stitcher.io?query=q');

        $this->assertNotContains('?', $uri->getQuery());
    }

    /** @test */
    public function query_must_be_percent_encoded() {
        $uri = new Uri('https://stitcher.io?query=%');

        $this->assertEquals('query=%25', $uri->getQuery());
    }

    /** @test */
    public function query_must_not_be_double_percent_encoded() {
        $uri = new Uri('https://stitcher.io?query=%25');

        $this->assertEquals('query=%25', $uri->getQuery());
    }

    /** @test */
    public function query_reserved_keys_must_not_be_percent_encoded() {
        $uri = new Uri('https://stitcher.io?a=a&b=&c=c');

        $this->assertEquals('a=a&b=&c=c', $uri->getQuery());
    }

    /** @test */
    public function with_query_must_retain_state() {
        $uri = new Uri('https://stitcher.io');
        $new = $uri->withQuery('a=a');

        $this->assertNotEquals($uri, $new);
    }

    /** @test */
    public function with_query_can_be_empty() {
        $uri = new Uri('https://stitcher.i?query/');
        $new = $uri->withQuery('');

        $this->assertEquals('', $new->getQuery());
    }

    /** @test */
    public function with_query_must_be_percent_encoded() {
        $uri = new Uri('https://stitcher.io');
        $new = $uri->withQuery('%');

        $this->assertEquals('%25', $new->getQuery());
    }

    /** @test */
    public function with_query_must_not_be_double_percent_encoded() {
        $uri = new Uri('https://stitcher.io');
        $new = $uri->withQuery('%25');

        $this->assertEquals('%25', $new->getQuery());
    }

    /** @test */
    public function with_query_reserved_keys_must_not_be_percent_encoded() {
        $uri = new Uri('https://stitcher.io');
        $new = $uri->withQuery('a=a&b=b');

        $this->assertEquals('a=a&b=b', $new->getQuery());
    }
}
