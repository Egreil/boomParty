<?php

namespace App\Tests;

use App\TryService;
use tryUnitTest;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    public function testTest(){
        $this->assertTrue(true);
    }
    public function testService(){
        //Arrange
        $tryService= new TryService();
        //act
        $result=$tryService->testService();
        //Assert
        $this->assertEquals(0,$result);
    }
}
