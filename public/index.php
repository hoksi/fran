<?php
require __DIR__ . '/../bootstrap.php';

echo qb()
    ->select('id, name')
    ->from('users')
    ->encryptLike('name', 'John')
    ->where('id', 1)
    ->getSql();

var_dump(new \Forbiz\Model\Test\Member());