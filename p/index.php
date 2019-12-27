<?php namespace _\lot\x;

function p($content) {
    $type = $this->type;
    if ($type && 'HTML' !== $type && 'text/html' !== $type) {
        return $content;
    }
    // Automatic paragraph converter
    $p = function($v) {
        $v = false !== \strpos($v, '<br') ? \preg_replace('/\s*<br(\s[^>]+)?(\s*\/)?>\s*/', '<br$1>', $v) : $v;
        $v = \rtrim(false !== \strpos($v, "\n") ? \preg_replace('/\n{3,}/', "\n\n", $v) : $v, ' ');
        return "\n" !== $v && 0 === \strpos($v, "\n") && "\n" === \substr($v, -1) ? "\n<p>" . \strtr(\trim($v), [
            "\n\n" => "</p>\n<p>",
            "\n" => "<br>\n"
        ]) . "</p>" : \strtr($v = \trim($v), [
            "\n" => "<br>\n"
        ]) . ("" !== $v ? \P : "");
    };
    // `1`: Disallow converting to paragraph in these block(s)
    // `2`: Allow converting to paragraph in these block(s)
    $blocks = [
        'blockquote' => 2,
        'body' => 2,
        'div' => 2,
        'footer' => 2,
        'header' => 2,
        'hr' => 2,
        'li' => 2,
        'main' => 2,
        'nav' => 2,
        'ol' => 2,
        'section' => 2,
        'ul' => 2,
        'dd' => 2,
        'dl' => 2,
        'dt' => 1,
        'figure' => 1,
        'form' => 1,
        'fieldset' => 1, // Must come after `form`
        'h1' => 1,
        'h2' => 1,
        'h3' => 1,
        'h4' => 1,
        'h5' => 1,
        'h6' => 1,
        'iframe' => 1,
        'p' => 1,
        'pre' => 1,
        'script' => 1,
        'style' => 1,
        'table' => 1,
        'textarea' => 1
    ];
    $parts = \preg_split('/(<!--[\s\S]*?-->|' . \implode('|', \array_filter((function($blocks) {
        foreach ($blocks as $k => &$v) {
            if (2 === $v) {
                $v = '<' . $k . '(?:\s[^>]+)?(?:\s*\/)?>|<\/' . $k . '>';
            } else if (1 === $v) {
                $v = '<' . $k . '(?:\s[^>]+)?>[\s\S]*?<\/' . $k . '>';
            } else {
                $v = null;
            }
        }
        return $blocks;
    })($blocks))) . ')/', "\n" . \trim(\n($content), "\n") . "\n", null, \PREG_SPLIT_NO_EMPTY | \PREG_SPLIT_DELIM_CAPTURE);
    $out = "";
    foreach ($parts as $v) {
        if ("" === $v || "\n" === $v || "\n\n" === $v) {
            continue;
        }
        if (0 === \strpos($v, '<!--') && '-->' === \substr($v, -3)) {
            $out .= $v;
        } else if ('<' === $v[0] && '>' === \substr($v, -1)) {
            $n = \explode(' ', \strstr(\substr($v, 1), '>', true), 2)[0];
            if (isset($blocks[\trim($n, '/')])) {
                $out .= "\n" . $v;
            } else {
                $out .= $p($v);
            }
        } else {
            $out .= $p($v);
        }
    }
    return \str_replace([\P . "\n", \P], "", $out);
}

\Hook::set([
    'page.content',
    'page.description'
], __NAMESPACE__ . "\\p", 2.1);
