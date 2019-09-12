<?php


namespace Seasx\SeasLogger\Tests;


use Seasx\SeasLogger\HtmlColor;

class HtmlColorTest extends TestCase
{
    public function testGetPossibleColors()
    {
        $this->assertEquals(count(HtmlColor::getPossibleColors()), count(HtmlColor::getPossibleColorsRGB()));
    }

    public function testGetColor()
    {
        $keys = HtmlColor::getPossibleColors();
        $values = HtmlColor::getPossibleColorsRGB();
        $index = rand(0, count($keys));
        $this->assertEquals(HtmlColor::getColor($keys[$index]), $values[$index]);
    }
}