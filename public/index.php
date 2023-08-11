<?php
require __DIR__ . '/../bootstrap.php';

/* @var $memberModel \Forbiz\Model\Test\Member */
$memberModel = fb_import('model.test.member');

echo '<xmp>';
var_dump($memberModel->getMenber('hoksi2k'));
echo '</xmp>';

tpl()->assign('test', 'test1-' . microtime(true));
tpl()->xprint('index.htm');

phpinfo();