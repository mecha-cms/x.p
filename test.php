<?php

$content = file_get_contents(__DIR__ . D . 'test');

echo '<pre style="border:3px solid #900;padding:3px;white-space:pre-wrap;word-wrap:break-word;"><code>' . htmlspecialchars($content) . '</code></pre>';
echo '<pre style="border:3px solid #090;padding:3px;white-space:pre-wrap;word-wrap:break-word;"><code>' . htmlspecialchars(fire("x\\p", [$content], (object) ['type' => 'HTML'])) . '</code></pre>';

exit;