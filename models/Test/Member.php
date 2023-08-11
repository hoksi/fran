<?php

namespace Forbiz\Model\Test;

class Member extends \Forbiz\Model\ForbizModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getMenber($id)
    {
        return qb()
            ->select('id')
            ->select('code')
            ->from('common_user')
            ->where('id', $id)
            ->exec()
            ->getResultArray();
    }
}