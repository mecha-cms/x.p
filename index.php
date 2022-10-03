<?php namespace x;

if (\defined("\\TEST") && 'x.p' === \TEST) {
    require __DIR__ . \D . 'test.php';
}

function p($content) {
    if (!$content) {
        return $content;
    }
    $type = $this->type;
    if ($type && 'HTML' !== $type && 'text/html' !== $type) {
        return $content;
    }
    // Automatic paragraph converter
    $p = static function ($v) {
        $v = false !== \strpos($v, '<br') ? \preg_replace('/\s*<br(\s[\p{L}\p{N}_:-]+(?:=(?:"[^"]*"|\'[^\']*\'|[^\/>]*))?)\/?>\s*/i', '<br$1>' . \P, $v) : $v;
        $v = \trim(false !== \strpos($v, "\n") ? \preg_replace('/\n{3,}/', "\n\n", $v) : $v, ' ');
        return "\n" !== $v && 0 === \strpos($v, "\n") && "\n" === \substr($v, -1) ? "\n<p>" . \strtr(\preg_replace('/\n[ ]*/', "\n", \trim($v)), [
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
        'caption' => 1,
        'details' => 2,
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
        'head' => 1,
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
        'summary' => 1,
        'table' => 1,
        'textarea' => 1
    ];
    $parts = \preg_split('/(<!--[\s\S]*?-->|' . \implode('|', \array_filter((static function ($tags) {
        foreach ($tags as $k => &$v) {
            if (2 === $v) {
                $v = '<' . $k . '(?:\s[\p{L}\p{N}_:-]+(?:=(?:"[^"]*"|\'[^\']*\'|[^\/>]*))?)*\/?>|<\/' . $k . '>';
            } else if (1 === $v) {
                $v = '<' . $k . '(?:\s[\p{L}\p{N}_:-]+(?:=(?:"[^"]*"|\'[^\']*\'|[^\/>]*))?)*>[\s\S]*?<\/' . $k . '>';
            } else {
                $v = null;
            }
        }
        unset($v);
        return $tags;
    })($blocks))) . ')/', "\n" . \trim(\n($content), "\n") . "\n", -1, \PREG_SPLIT_NO_EMPTY | \PREG_SPLIT_DELIM_CAPTURE);
    $out = "";
    foreach ($parts as $part) {
        if ("" === $part || "\n" === $part || "\n\n" === $part) {
            continue;
        }
        if (0 === \strpos($part, '<!--') && '-->' === \substr($part, -3)) {
            $out .= $part;
            continue;
        }
        if ('<' === $part[0] && '>' === \substr($part, -1)) {
            $n = \strtok(\substr($part, 1, -1), " \n\r\t>");
            if (isset($blocks[\trim($n, '/')])) {
                $out .= "\n" . $part;
                continue;
            }
            $out .= $p($part);
            continue;
        }
        $out .= $p($part);
    }
    $out = \trim(\strtr($out, [
        \P . "\n" => "",
        \P => "\n"
    ]));
    return "" !== $out ? $out : null;
}

\Hook::set([
    'page.content',
    'page.description'
], __NAMESPACE__ . "\\p", 2.1);