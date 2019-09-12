<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Tests;


use Seasx\SeasLogger\Context;

class ContextTest extends TestCase
{
    public function testGet()
    {
        \Co\Run(function () {
            Context::set('key', 'value');
            $this->assertEquals('value', Context::get('key'));
        });
    }

    public function testHas()
    {
        \Co\Run(function () {
            Context::set('key', 'value');
            $this->assertTrue(Context::has('key'));
        });
    }

    public function testNotHas()
    {
        \Co\Run(function () {
            $this->assertFalse(Context::has('key'));
        });
    }

    public function testDelete()
    {
        \Co\Run(function () {
            Context::set('key', 'value');
            $this->assertEquals('value', Context::get('key'));
            Context::delete('key');
            $this->assertFalse(Context::has('key'));
        });
    }
}