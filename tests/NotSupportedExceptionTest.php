<?php


namespace Seasx\SeasLogger\Tests;


use Seasx\SeasLogger\Exceptions\NotSupportedException;

class NotSupportedExceptionTest extends TestCase
{

    /**
     * @expectedException  \Seasx\SeasLogger\Exceptions\NotSupportedException
     */
    public function testThrow()
    {
        $exception = new NotSupportedException("Test case");
        throw $exception;
    }

    /**
     * @expectedException  \Seasx\SeasLogger\Exceptions\NotSupportedException
     * @expectedExceptionMessage Test case
     */
    public function testMessage(){
        $exception = new NotSupportedException("Test case");
        throw $exception;
    }
}