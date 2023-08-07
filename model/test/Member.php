<?php

namespace Forbiz\Model\Test;

class Member extends \Forbiz\Model\ForbizModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getNenber($id)
    {
        return qb()
            ->select('id, name')
            ->from('users')
            ->encryptLike('name', 'John')
            ->where('id', 1)
            ->getSql();
    }
}