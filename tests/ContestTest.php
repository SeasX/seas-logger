<?php


namespace Seasx\SeasLogger\Tests;


use Seasx\SeasLogger\Context;

class ContestTest extends TestCase
{
    public function testGet()
    {
        go(function () {
            Context::set('key', 'value');
            $this->assertEquals('value', Context::get('key'));
        });
    }

    public function testHas()
    {
        go(function () {
            Context::set('key', 'value');
            $this->assertTrue(Context::has('key'));
        });
    }

    public function testNotHas()
    {
        go(function () {
            $this->assertFalse(Context::has('key'));
        });
    }

    public function testDelete()
    {
        go(function () {
            Context::set('key', 'value');
            $this->assertEquals('value', Context::get('key'));
            Context::delete('key');
            $this->assertFalse(Context::has('key'));
        });
    }
}