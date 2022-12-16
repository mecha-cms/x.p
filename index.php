<?php namespace x;

function p($content) {
    if (!$content) {
        return $content;
    }
    $type = $this->type;
    if ($type && 'HTML' !== $type && 'text/html' !== $type) {
        return $content;
    }
    $blocks = [
        // Character data section
        '<\!\[CDATA\[[\s\S]*?\]\]>',
        // Comment
        '<\!--[\s\S]*?-->',
        // Document type
        '<\!(?:"[^"]*"|\'[^\']*\'|[^>])*>',
        // Processing instruction
        '<\?(?:"[^"]*"|\'[^\']*\'|[^>?])*\?>'
    ];
    // `1`: Disallow converting to paragraph in these block(s)
    // `2`: Allow converting to paragraph in these block(s)
    foreach ([
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
    ] as $k => $v) {
        if (2 === $v) {
            $blocks[$k] = '<' . $k . '(?:\s[\p{L}\p{N}_:-]+(?:=(?:"[^"]*"|\'[^\']*\'|[^\/>]*))?)*\/?>|<\/' . $k . '>';
        } else if (1 === $v) {
            $blocks[$k] = '<' . $k . '(?:\s[\p{L}\p{N}_:-]+(?:=(?:"[^"]*"|\'[^\']*\'|[^\/>]*))?)*>(?:(?R)|[\s\S])*?<\/' . $k . '>';
        }
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
    $parts = \preg_split('/(' . \implode('|', $blocks) . ')/', "\n" . \trim(\n($content), "\n") . "\n", -1, \PREG_SPLIT_NO_EMPTY | \PREG_SPLIT_DELIM_CAPTURE);
    $out = "";
    foreach ($parts as $part) {
        if ("" === $part || "\n" === $part || "\n\n" === $part) {
            continue;
        }
        if (
            // Character data section
            0 === \strpos($part, '<![CDATA[') && ']]>' === \substr($part, -3) ||
            // Comment
            0 === \strpos($part, '<!--') && '-->' === \substr($part, -3) ||
            // Document type
            0 === \strpos($part, '<!') && '>' === \substr($part, -1) ||
            // Processing instruction
            0 === \strpos($part, '<?') && '?>' === \substr($part, -2)
        ) {
            $out .= $part;
            continue;
        }
        if ('<' === $part[0] && '>' === \substr($part, -1)) {
            $n = \trim(\strtok(\substr($part, 1, -1), " \n\r\t>"), '/');
            if (isset($blocks[$n]) && !\is_numeric($n)) {
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

\Hook::set('page.content', __NAMESPACE__ . "\\p", 2.1);

if (\defined("\\TEST") && 'x.p' === \TEST && \is_file($test = __DIR__ . \D . 'test.php')) {
    require $test;
}