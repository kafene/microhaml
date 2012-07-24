<?php

require_once('microhaml.php');
include_once('ndebugger.php');

NDebugger::enable();
ob_start();

$haml = file_get_contents('test.haml');
$php = Microhaml::parseFile('test.haml');
print '<pre>' . $haml . '</pre>' .
      '<pre>' . htmlspecialchars($php) . '</pre>';

$title = 'Hello, world.';
$list = array('one', 'two');
$content = 'What a nice day.';
eval('?><div class="out">' . $php . '</div>');
$page = ob_get_clean();

?>

<?=$page?>

<style>
  html {background-color: #eaeaea; }
  body {max-width: 46em; margin: .5em auto; padding: 3em 4em; font-size: 14px; background-color: white; border: 1px solid #ccc}
  pre, .out {max-height: 15em; background-color: #fbfbfb; border: 1px solid #ccc; overflow: auto; padding: .5em 1em }
  a {color: #58f; text-decoration: none}
  a:hover {color: #69f; text-decoration: underline}
</style>
