<?php namespace _\page;

function paragraph($content) {
    if ($this['type'] === 'HTML') {
        return (new \To\Paragraph)->apply($content);
    }
    return $content;
}

\Hook::set([
    '*.content',
    '*.description'
], __NAMESPACE__ . "\\paragraph", 2.1);