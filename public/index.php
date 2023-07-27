<?php
require __DIR__ . '/../bootstrap.php';

/* @var $memberModel \Forbiz\Model\Test\Member */
$memberModel = fb_import('model.test.member');

var_dump($memberModel->getNenber(1));