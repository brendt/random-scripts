<?php

namespace Http\Uri;

use Http\Uri;
use PHPUnit\Framework\TestCase;

class UserInfoTest extends TestCase
{
    /** @test */
    public function no_user_info_must_return_an_empty_string() {
        $uri = new Uri('https://stitcher.io');

        $this->assertEquals('', $uri->getUserInfo());
    }

    /** @test */
    public function user_name_present() {
        $uri = new Uri('https://brendt@stitcher.io');

        $this->assertEquals('brendt', $uri->getUserInfo());
    }

    /** @test */
    public function user_name_and_password_present() {
        $uri = new Uri('https://brendt:pass@stitcher.io');

        $this->assertEquals('brendt:pass', $uri->getUserInfo());
    }

    /** @test */
    public function with_user_info_must_retain_state() {
        $uri = new Uri('stitcher.io');
        $new = $uri->withUserInfo('brendt');

        $this->assertNotEquals($uri, $new);
    }

    /** @test */
    public function with_user_info_sets_user() {
        $uri = new Uri('https://stitcher.io');
        $new = $uri->withUserInfo('brendt');

        $this->assertEquals('brendt', $new->getUserInfo());
    }

    /** @test */
    public function with_user_info_sets_user_and_password() {
        $uri = new Uri('https://stitcher.io');
        $new = $uri->withUserInfo('brendt', 'pass');

        $this->assertEquals('brendt:pass', $new->getUserInfo());
    }

    /** @test */
    public function with_user_info_supports_empty_value() {
        $uri = new Uri('https://brent:pass@stitcher.io');
        $new = $uri->withUserInfo('');

        $this->assertEquals('', $new->getUserInfo());
    }
}
