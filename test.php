<?php

require_once 'microhaml.php';

define('TEST', 'test.haml');

function h($s){
  return htmlspecialchars($s);
}

$title = 'Hello, world.';
$list = array('one', 'two');
$content = 'What a nice day.';

?>
<h3>Haml</h3>
<pre><?php echo h(file_get_contents(TEST))?></pre>

<h3>Html</h3>
<pre><?php echo h(Microhaml::parseFile(TEST))?></pre>

<h3>Html with expanded style</h3>
<pre><?php echo h(Microhaml::parseFile(TEST, true))?></pre>

<h3>Output</h3>
<div class="out"><?php echo eval('?>' . Microhaml::parseFile(TEST))?></pre>

<style>
  html {background-color: #eaeaea; }
  body {max-width: 46em; margin: .5em auto; padding: 3em 4em; font-size: 14px; background-color: white; border: 1px solid #ccc}
  pre, .out {max-height: 15em; background-color: #fbfbfb; border: 1px solid #ccc; overflow: auto; padding: .5em 1em }
  a {color: #58f; text-decoration: none}
  a:hover {color: #69f; text-decoration: underline}
</style>
