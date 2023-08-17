<?php

class MemberModelTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Forbiz\Model\Test\Member
     */
    public function testGetMember()
    {
        /* @var $member \Forbiz\Model\Test\Member */
        $member =fb_import('model.test.member');
        $result = $member->getMenber('hoksi2k');
        $this->assertEquals('hoksi2k', $result[0]['id']);
    }
}