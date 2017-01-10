<?php
namespace JC\TaskLocker\Tests;

use JC\TaskLocker\Locker;

class LockerTest extends \PHPUnit_Framework_TestCase
{

    public function testLocker()
    {
        $locker = new Locker();
        $locker->setName('app1');
        $this->assertEquals('app1', $locker->getName());

        $locker->setExpiry(10); //Seconds
        $this->assertEquals('-10 seconds', $locker->getExpiry());

        $locker->setRuntimePath('/tmp');
        $this->assertEquals('/tmp', $locker->getRuntimePath());

        $locker->setFileExtension('.lck');
        $this->assertEquals('.lck', $locker->getFileExtension());

        $this->assertEquals(1, $locker->lock());
        $this->assertEquals(1, $locker->isLocked());
        $this->assertEquals(1, $locker->unlock());
        $this->assertEquals(0, $locker->isLocked());
    }
}