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
        $cnt = qb()->insertBatch('common_user', $data);

        $this->AssertEquals(1000, $cnt);

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
                'user_name' => 'test'.($i+1),
                'user_password' => 'test'.$i,
                'user_email' => 'email'.$i
            ];
        }
        $cnt = qb()->updateBatch('common_user', $data, 'user_id', 10);

        $this->AssertEquals(1000, $cnt);
    }

    /**
     * @covers \CI_Qb::delete
     */
    public function testDelete()
    {
        $ret = qb()
            ->like('user_id', 'test')
            ->delete('common_user')
            ->exec();

        $this->AssertTrue($ret);
    }

}