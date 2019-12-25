<?php namespace _\lot\x;

function p($content) {
    $type = $this->type;
    if ($type && 'HTML' !== $type && 'text/html' !== $type) {
        return $content;
    }
    // Automatic paragraph converter
    $p = function($v) {
        return '<p>' . \strtr(\n(\trim($v)), [
            "\n\n" => "</p>\n<p>",
            "\n" => "<br>\n"
        ]) . '</p>';
    };
    // Allow automatic paragraph converter in these block(s)
    $blocks_a = [
        'blockquote',
        'div',
        'hr',
        'li',
        'ol',
        'section',
        'ul'
    ];
    // Disallow automatic paragraph converter in these block(s)
    $blocks_b = [
        'dl',
        'dt',
        'dd', // Must come after `dl`
        'figure',
        'form',
        'fieldset', // Must come after `form`
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'iframe',
        'nav',
        'p',
        'pre',
        'script',
        'section',
        'style',
        'table',
        'textarea'
    ];
    $parts = \preg_split('/\s*(<!--[\s\S]*?-->|' . \implode('|', (function($blocks) {
        foreach ($blocks as &$v) {
            $v = '<' . $v . '(?:\s[^>]+)?>|<\/' . $v . '>';
        }
        return $blocks;
    })($blocks_a)) . '|' . \implode('|', (function($blocks) {
        foreach ($blocks as &$v) {
            $v = '<' . $v . '(?:\s[^>]+)?>[\s\S]*?<\/' . $v . '>';
        }
        return $blocks;
    })($blocks_b)) . ')\s*/', $content, null, \PREG_SPLIT_NO_EMPTY | \PREG_SPLIT_DELIM_CAPTURE);
    $out = "";
    foreach ($parts as $v) {
        if ("" === ($v = \trim($v))) {
            continue;
        }
        if (0 === \strpos($v, '<!--') && '-->' === \substr($v, -3)) {
            $out .= "\n" . $v;
        } else if ('<' === $v[0] && '>' === \substr($v, -1)) {
            $n = \explode(' ', \strstr(\substr($v, 1), '>', true), 2)[0];
            if (\in_array(\trim($n, '/'), $blocks_a) || \in_array($n, $blocks_b)) {
                $out .= "\n" . $v;
            } else {
                $out .= "\n" . $p($v);
            }
        } else {
            $out .= "\n" . $p($v);
        }
    }
    return \substr($out, 1);
}

\Hook::set([
    'page.content',
    'page.description'
], __NAMESPACE__ . "\\p", 2.1);
