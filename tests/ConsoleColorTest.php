<?php


namespace Seasx\SeasLogger\Tests;

use Seasx\SeasLogger\ConsoleColor;

/**
 * Class ConsoleColorTest
 * @package Seasx\SeasLogger\Tests
 */
class ConsoleColorTest extends TestCase
{
    public function testApply()
    {
        $color = new ConsoleColor();
        $this->assertEquals('[none test]', $color->apply('none', '[none test]'));
        $this->assertEquals('[32m[green test][0m', $color->apply('green', '[green test]'));
    }

    public function testSetForceStyle(){
        $color = new ConsoleColor();
        $color->setForceStyle(true);
        $this->assertTrue($color->isStyleForced());
        $color->setForceStyle(false);
        $this->assertFalse($color->isStyleForced());
    }
}