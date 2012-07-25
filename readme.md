# Microhaml

Small, simple and stupid haml parser written in PHP.

It doesn't have all functionality of original Haml parser.

## Changes

- only round brackets are supported `%html(lang="en")`
- there are no filters like `:plain`
- no self closing tags `%meta(...)/`
- no whitespace removals `<` and `>`
- no conditional comments `/[if IE]`
- `= $var` is allways escaped
- a bunch of other stuffs

## What works

- `%p` elements
- `.class#id` implicit div
- html 5 doctype from `!!!`
- `/ coment` to `<?php // comment ?>`
- `- if ($var)` no need for opening and closing brackets
- `= $var` to `<?php echo htmlspecialchars($var) ?>`

## Todo

- implement auto-closing `{{#block}}` syntax from mustache
- add support for `{{variable}}` syntax
- add self closing tags `%meta/`
- maybe filters `:plain`, `:sass`

