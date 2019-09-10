<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Tests;

use Seasx\SeasLogger\ArrayHelper;

/**
 * Class ArrayHelperTest
 * @package Seasx\SeasLogger\Tests
 */
class ArrayHelperTest extends TestCase
{
    public function testGetValue()
    {
        $this->assertEquals('test', ArrayHelper::getValue([
            'key' => 'test'
        ], 'key'));
    }

    public function testGetValueDefault()
    {
        $this->assertEquals('test', ArrayHelper::getValue([
        ], 'key', 'test'));
    }

    public function testRemove()
    {
        $value = [
            'key' => 'test'
        ];
        $this->assertEquals('test', ArrayHelper::remove($value, 'key'));
        $this->assertArrayNotHasKey('test', $value);
    }
}