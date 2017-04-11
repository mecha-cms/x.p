<?php

To::plug('paragraph', function($text) {
    return (new Converter\Paragraph)->run($text);
});