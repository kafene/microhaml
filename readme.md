# Microhaml

Small, simple and stupid haml parser written in PHP.

It doesn't have all functionality of original Haml parser.

## What works

- `%p` elements
- `.class#id` implicit div
- html 5 doctype from `!!!`
- `/ coment` to `<?php // comment ?>`
- `- if ($var)` no need for opening and closing brackets
- `= $var` to `<?php echo htmlspecialchars($var) ?>`
- round brackets for parameters `%html(lang="en")`

## Todo

- implement auto-closing `{@array}` syntax from microtpl
- add self closing tags `%meta/`
- filters `:plain`, `:css` and `:sass`
- conditional comments `/[if IE]`
