<?php

class ReadBeanTest extends \PHPUnit\Framework\TestCase
{
//    /**
//     * @covers \RedBeanPHP\OODBBean
//     */
//    public function testReadBeanLoad()
//    {
//        $user = rb();
//        $this->AssertInstanceOf('\R', $user);
//    }
//
//    /**
//     * @covers \R::getPDO
//     */
//    public function testGetPdo()
//    {
//        $pdo = rb();
//
//        $this->AssertEquals('PDO', get_class($pdo));
//    }

    /**
     * @covers \RedBeanPHP\OODBBean
     */
    public function testCrudTable()
    {
        $book = rb()::dispense('testbook');

        $book->title = 'Learn to Program';
        $book->rating = 10;
        $book->author = 'Chris Pine';

        $ret = rb()::store($book);

        $this->AssertEquals($ret, $ret);
    }


}