<?php
# micro haml for starship framework

function haml($src, $nice = false){
  $list['html'] = array();
  $list['php'] = array();
  $list['element'] = array();
  $html = NULL;
  $regex = array(
    'element' => '/^[%#\.\=\-]/',
    'code' => '/^%' . '([^ .#\(=\-]+)' . '.*$/',
    'attr' => '/^[^\(=\-]+' . '\(([^\)]+)\)' . '.*$/',
    'ids' => '/^[^ \.#\(=\-]*' . '([\.#][^ \(=]*).*$/',
    'text' => '/^[^ \(=\-]+(\([^\)]*\))? (.*)$/',
    'php' => '/^([^ \(]+(\([^\)]*\))?)?([=\-]) (.*)$/',
    'block' => '/^(if|while|do|for|foreach|switch|else).*$/',
    'doctype' => '/^\!\!\!( .*)?/',
    'comment' => '/^\/.*/',
  );
  $src = str_replace("\r", '', $src);
  $lines = explode("\n", $src. "\n");

  # proccess lines
  foreach ($lines as $n => $line){
    $code = NULL;
    $attr = NULL;
    $ids = array('haml'=>NULL, 'html'=>NULL, 'all'=>NULL, '.'=>NULL, '#'=>NULL,);
    $php = NULL;
    $text = NULL;
    $opening = NULL;
    $closing = NULL;
    $list['element'][$n] = NULL;
    $bracket = NULL;
    $indent = intval(strlen(preg_replace('/[^ ].*/', '', $line)) / 2);
    $line = trim($line);

    # it's element
    if(preg_match($regex['element'], $line)){

      # get code prefixed % (body, span...)
      if (preg_match($regex['code'], $line))
        $code = preg_replace($regex['code'], '\1', $line);

      # get element's attributes (value=, title=...)
      if (preg_match($regex['attr'], $line))
        $attr = preg_replace($regex['attr'], ' \1', $line);

      # get class and id prefixed [.#]
      if (preg_match($regex['ids'], $line)){
        if (!$code) $code = 'div';
        $ids['all'] = preg_replace($regex['ids'], ' \1', $line);
        $ids['all'] = preg_split('/([.#])/', $ids['all'], NULL, PREG_SPLIT_DELIM_CAPTURE);
        foreach($ids['all'] as $k => $v){
          if (!trim($v, ' .#]')) continue;
          $ids[$ids['all'][$k-1]][] = trim($v, '-');
        }
        if (is_array($ids['.']))
          $ids['html'] .= ' class="' . implode(' ', $ids['.']) . '"';
        if (is_array($ids['#']))
          $ids['html'] .= ' id="'    . implode(' ', $ids['#']) . '"';
      }

      # get text
      if (preg_match($regex['text'], $line))
        $text = preg_replace($regex['text'], '\2', $line);

      # save element
      $list['element'][$n] = $code;
      if ($code)
        $opening = '<' . $code . $attr . $ids['html'] . '>';
      # php
      if (preg_match($regex['php'], $line)){
        $php = preg_replace($regex['php'], '\4', $line);
        $mark = preg_replace($regex['php'], '\3', $line);
        if ($mark == '=')
          $opening .= '<?php echo ' . $php . '; ?>';
        if ($mark == '-'){
          if(preg_match($regex['block'], $php)){
            $bracket = '{';
          }
          $opening .= '<?php ' . $php . $bracket. ' ?>';
        }
      }
    }

    # it's doctype
    elseif(preg_match($regex['doctype'], $line) && $n == 0)
      $text = '<!DOCTYPE html>';

    # it's comment
    elseif(preg_match($regex['comment'], $line))
      $text = "<?php /$line ?>";

    # it's text
    else $text = $line;

    # closing
    $max = max(key(array_slice($list['html'], -1, 1, true)), key(array_slice($list['php'], -1, 1, true))+1);
    for ($j = 0; $j <= $max; $j++){
      if ($indent > $j) continue;
      $spaces = '';
      $newline = '';
      if ($nice){
        for ($i=0; $i<$j; $i++) $spaces .= '  ';
        $newline = "\n";
      }
      if (isset($list['html'][$j]) && isset($list['element'][$list['html'][$j]]))
        $closing = $spaces . "</" . $list['element'][$list['html'][$j]] . ">" . $newline . $closing;
      if (isset($list['php'][$j]))
        $closing = $spaces . "<?php } ?>" . $newline . $closing;
    }

    # remove closed from lists
    $list['html'] = array_slice($list['html'], 0, $indent, true);
    $list['php'] = array_slice($list['php'], 0, $indent, true);

    # output
    $html .= $closing;
    if ($nice) for ($i=0; $i<$indent; $i++) $html .= '  ';
    $html .= $opening;
    $html .= $text . "\n";

    # add opened to lists
    $list['html'][$indent] = $n; 
    if (isset($bracket)) $list['php'][$indent] = true;
  }
  #$html = preg_replace('/\?\><\?php/', '', $html);
  return trim($html, " \n");
}
