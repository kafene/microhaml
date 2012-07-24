<?php

# Micro Haml
# Convert haml template to phtml.
# Unumin 2012

class Microhaml {

  const REGEX_ELEMENT = '/^[%#\.\=\-].*$/';
  const REGEX_COMMENT = '/^\/.*$/';
  const REGEX_DOCTYPE = '/^\!\!\!( .*)?$/';
  const REGEX_CODE    = '/^%([a-z0-9]+).*$/i';
  const REGEX_ATTR    = '/^[^\(=\-]+\(([^\)]+)\).*$/';
  const REGEX_STYLE   = '/^[^ \.#\(=\-]*([\.#][^ \(=]*).*$/';
  const REGEX_TEXT    = '/^[^ \(=\-]+(\([^\)]*\))? (.*)$/';
  const REGEX_PHP     = '/^([^ \(]+(\([^\)]*\))?)?([=\-]) (.*)$/';
  const REGEX_BLOCK   = '/^(if|while|do|for|foreach|switch|else).*$/';


  # ---------------------------------------------
  # parseFile
  # ---------------------------------------------
  static public function parseFile($file){
    return self::parse(file_get_contents($file));
  }

  # ---------------------------------------------
  # parse
  # ---------------------------------------------
  static public function parse($source){
    $source = str_replace("\r", '', $source);
    $lines = explode("\n", $source . "\n");
    $indent = 0;
    $output = null;
    $closing = array();
  
    # proccess lines
    foreach ($lines as $n => $line){
      $indentPrev = $indent;
      $indent = (strlen($line) - strlen(ltrim($line))) / 2;
      $indentSpaces = str_repeat('  ', $indent);
      $line = trim($line);

      # closing
      $closingLine = implode('', array_reverse(array_slice($closing, $indent)));
      $closing = array_slice($closing, 0, $indent);

      # get element
      if (preg_match(self::REGEX_ELEMENT, $line)){
        $element = self::getElement($line);
        $line = $element[0];
        $closing[$indent] = $element[1];
      }

      # get comment
      elseif (preg_match(self::REGEX_COMMENT, $line))
        $line = "<?php /$line ?>";

      # get doctype
      elseif (preg_match(self::REGEX_DOCTYPE, $line))
        $line = '<!DOCTYPE html>';

      # format template
      $output .= $closingLine . "\n" . $indentSpaces . $line;
    }

    return trim($output);
  }

  # ---------------------------------------------
  # getElement
  # ---------------------------------------------
  static public function getElement($line){
    $attr = $code = $php = $mark = $output = $closing = null;
    $style = $styleAll = $styleClass = $styleId = null;

    # get code prefixed % (body, span...)
    if (preg_match(self::REGEX_CODE, $line))
      $code = preg_replace(self::REGEX_CODE, '\1', $line);

    # get element's attributes (value=, title=...)
    if (preg_match(self::REGEX_ATTR, $line))
      $attr = preg_replace(self::REGEX_ATTR, ' \1', $line);

    # get class and id prefixed [.#]
    if (preg_match(self::REGEX_STYLE, $line)){
      if (!$code)
        $code = 'div';
      $styleAll = preg_replace(self::REGEX_STYLE, ' \1', $line);
      $styleAll = preg_split('/([.#])/', $styleAll, NULL, PREG_SPLIT_DELIM_CAPTURE);
      for ($i = 1; $i < count($styleAll); $i += 2){
        if ($styleAll[$i] == '.')
          $styleClass[] = $styleAll[$i + 1];
        elseif ($styleAll[$i] == '#')
          $styleId[] = $styleAll[$i + 1];
      }
      if (is_array($styleClass))
        $style .= ' class="' . implode(' ', $styleClass) . '"';
      if (is_array($styleId))
        $style .= ' id="'    . implode(' ', $styleId) . '"';
    }

    # format html
    if ($code){
      $output = '<' . $code . $attr . $style . '>';
      $closing = "</$code>";
    }

    # get text
    if (preg_match(self::REGEX_TEXT, $line))
      $output .= preg_replace(self::REGEX_TEXT, '\2', $line);

    # php
    if (preg_match(self::REGEX_PHP, $line)){
      $php = preg_replace(self::REGEX_PHP, '\4', $line);
      $mark = preg_replace(self::REGEX_PHP, '\3', $line);
      if ($mark == '=')
        $output .= '<?php echo htmlspecialchars(' . $php . ', ENT_QUOTES); ?>';
      if ($mark == '-'){
        if(preg_match(self::REGEX_BLOCK, $php)){
          $php .= '{';
          $closing = '<?php } ?>' . $closing;
        }
        $output .= "<?php $php ?>";
      }
    }

    return array($output, $closing);
  }

}
