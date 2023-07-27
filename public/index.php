<?php
require __DIR__ . '/../bootstrap.php';
echo 'Hello Fran!';

echo qb()
    ->select('id, name')
    ->from('users')
    ->where('id', 1)
    ->getSql();
