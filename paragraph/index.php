<?php

require __DIR__ . DS . 'engine' . DS . 'plug' . DS . 'to.php';

function fn_converter_paragraph_replace($content, $lot) {
    if (!is_string($content) || !isset($lot['type']) || $lot['type'] !== 'HTML') {
        return $content;
    }
    return To::paragraph($content);
}

Hook::set([
    'page.description',
    'page.content',
    'comment.description',
    'comment.content'
], 'fn_converter_paragraph_replace', 2);