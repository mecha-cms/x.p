Automatic Paragraph Extension for [Mecha](https://github.com/mecha-cms/mecha)
=============================================================================

![Code Size](https://img.shields.io/github/languages/code-size/mecha-cms/x.p?color=%23444&style=for-the-badge)

This extension inserts `<p>` and `<br>` tags automatically to the page content by detecting the surrounding `\n` characters. This extension will pay attention to which parts need to be automated and which do not. For example, this extension will never insert paragraph tags within the `<pre>`, `<script>`, `<style>` and `<textarea>` elements. This extension works only for pages with `type` of `HTML` and `text/html`.

---

Release Notes
-------------

### 2.2.2

 - Return `null` where possible.
 - [@mecha-cms/mecha#96](https://github.com/mecha-cms/mecha/issues/96)

### 2.2.0

 - Reduce regular expressions.