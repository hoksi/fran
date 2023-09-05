<?php

class ReadBeanTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \R
     */
    public function testReadBeanLoad()
    {
        $user = \R::dispense('common_user');
        $this->AssertInstanceOf('\R', $user);
    }


}