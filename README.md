Automatic Paragraph Plugin for Mecha
====================================

Paragraph
---------

The `To\Paragraph` class will convert double line break to `<p> … </p>` and single line break to `<br>`.

### Before

~~~ .html
Lorem ipsum dolor sit amet.

Lorem ipsum dolor sit amet.
Lorem ipsum dolor sit amet.

<pre><code>Lorem ipsum dolor sit amet.

Lorem ipsum dolor sit amet.</code></pre>

<blockquote>
Lorem ipsum dolor sit amet.

Lorem ipsum dolor sit amet.
</blockquote>

<p>Lorem ipsum dolor sit amet.</p>
~~~

### After

~~~ .html
<p>Lorem ipsum dolor sit amet.</p>
<p>Lorem ipsum dolor sit amet.<br>
Lorem ipsum dolor sit amet.</p>
<pre><code>Lorem ipsum dolor sit amet.

Lorem ipsum dolor sit amet.</code></pre>
<blockquote>
<p>Lorem ipsum dolor sit amet.</p>
<p>Lorem ipsum dolor sit amet.</p>
</blockquote>
<p>Lorem ipsum dolor sit amet.</p>
~~~

---

### Class Usage

~~~ .php
require '../engine/kernel/to.paragraph.php';

$parser = new To\Paragraph;
$parser->ignore = 'pre|code'; // Settings…

echo $parser->apply('Lorem ipsum dolor sit amet.');
~~~