<?php

require_once('microhaml.php');

$haml = file_get_contents('test.haml');
$php = Microhaml::parseFile('test.haml');
print '<pre>' . $haml . '<hr>' . htmlspecialchars($php) . '</pre>';

$title = 'Hello, world.';
$list = array('one', 'two');
$content = 'What a nice day.';
eval('?>'. $php);
