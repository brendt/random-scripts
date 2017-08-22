<?php

namespace Brendt;

use PHPUnit\Framework\TestCase;

class ArrayFinderTest extends TestCase
{
    /** @test */
    public function create_with_array() {
        $array = ArrayFinder::fromArray([]);

        $this->assertInstanceOf(ArrayFinder::class, $array);
    }

    /** @test */
    public function get_with_array_access() {
        $array = new ArrayFinder([
            'country' => [
                'code' => 'BE',
            ],
        ]);

        $this->assertEquals('BE', $array['country.code']);
    }

    /** @test */
    public function set_with_array_access() {
        $array = new ArrayFinder();
        $array['country.code'] = 'BE';

        $this->assertEquals('BE', $array['country']['code']);
    }

    /** @test */
    public function simple_get() {
        $array = new ArrayFinder([
            'country' => [
                'code' => 'BE',
            ],
        ]);

        $this->assertEquals('BE', $array->get('country.code'));
    }

    /** @test */
    public function multiple_get() {
        $array = new ArrayFinder([
            ['country' => ['code' => 'BE']],
            ['country' => ['code' => 'FR']],
            ['country' => ['code' => 'NL']],
        ]);

        $result = $array->get('*.country.code');

        $this->assertCount(3, $result);
        $this->assertEquals('BE', $result[0]);
        $this->assertEquals('FR', $result[1]);
        $this->assertEquals('NL', $result[2]);
    }

    /** @test */
    public function nested_multiple_get() {
        $array = new ArrayFinder([
            'countries' => [
                ['code' => 'BE'],
                ['code' => 'FR'],
                ['code' => 'NL'],
            ]
        ]);

        $result = $array->get('countries.*.code');

        $this->assertCount(3, $result);
        $this->assertEquals('BE', $result[0]);
        $this->assertEquals('FR', $result[1]);
        $this->assertEquals('NL', $result[2]);
    }

    /** @test */
    public function simple_set() {
        $array = (new ArrayFinder())->set('country.code', 'BE');

        $this->assertTrue(isset($array['country']['code']));
        $this->assertEquals('BE', $array['country']['code']);
    }

    /** @test */
    public function multiple_set() {
        $array = new ArrayFinder([
            ['country' => ['code' => 'BE']],
            ['country' => ['code' => 'FR']],
            ['country' => ['code' => 'NL']],
        ]);

        $array->set('*.country.parsed', true);

        $this->assertTrue($array[0]['country']['parsed']);
        $this->assertTrue($array[1]['country']['parsed']);
        $this->assertTrue($array[2]['country']['parsed']);
    }

    /** @test */
    public function set_with_callable() {
        $array = new ArrayFinder();

        $array->set('country.code', function () {
            return 'BE';
        });

        $this->assertEquals('BE', $array['country']['code']);
    }

    /** @test */
    public function multiple_set_with_callable() {
        $array = new ArrayFinder([
            ['country' => ['code' => 'BE']],
            ['country' => ['code' => 'FR']],
            ['country' => ['code' => 'NL']],
        ]);

        $array->set('*.country.name', function ($element, $key) {
            return "{$key}-{$element['country.code']}";
        });

        $this->assertEquals('0-BE', $array[0]['country']['name']);
        $this->assertEquals('1-FR', $array[1]['country']['name']);
        $this->assertEquals('2-NL', $array[2]['country']['name']);
    }
}
