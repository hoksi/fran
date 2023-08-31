<?php

class QueryBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \CI_Qb
     */

    public function testCI_DBLoad()
    {
        $db = qb();
        $this->AssertInstanceOf('\CI_Qb', $db);
    }

    /**
     * @covers \CI_Qb::insertBatch
     */
    public function testInsertBatch()
    {
        $data = [];
        for($i = 0; $i < 1000; $i++) {
            $data[] = [
                'user_id' => 'test'.$i,
                'user_name' => 'test'.$i,
                'user_password' => 'test'.$i,
                'user_email' => 'email'.$i
            ];
        }
        $sql = qb()->insertBatch('common_user', $data);

        $this->AssertEquals(10, count($sql));

    }

    /**
     * @covers \CI_Qb::updateBatch
     */
    public function testUpdateBatch()
    {
        $data = [];
        for($i = 0; $i < 1000; $i++) {
            $data[] = [
                'user_id' => 'test'.$i,
                'user_name' => 'test'.$i,
                'user_password' => 'test'.$i,
                'user_email' => 'email'.$i
            ];
        }
        $sql = qb()->updateBatch('common_user', $data, 'user_id', 10);

        $this->AssertEquals(100, count($sql));
    }

}