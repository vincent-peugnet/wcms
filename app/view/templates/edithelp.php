

    <h4>update shortcut</h4>
    <kbd>CTRL</kbd> + <kbd>S</kbd>
    <br><br>
    <kbd>ALT</kbd> + <kbd>S</kbd>
    <h4>display shortcut</h4>
    <kbd>CTRL</kbd> + <kbd>D</kbd>
    <h4>Search</h4>
    <kbd>ALT</kbd> + <kbd>F</kbd>
    <p><kbd>ENTER</kbd> to find next.</p>
    <h4>Replace</h4>
    <kbd>CTRL</kbd> + <kbd>SHIFT</kbd> + <kbd>F</kbd>

    <h4>Markdown synthax</h4>
    <ul>
        <li><code>[<i>hello</i>](<i>PAGE_ID/URL</i>)</code>link</li>
        <li><code>![<i>alt</i>](<i>imagepath</i>)</code>img</li>
        <li><code><<i>e@mail.net</i>></code></li>
        <li><code># <i>h1 title</i></code></li>
        <li><code>## <i>h2 title</i></code></li>
        <li><code>*<i>emphasis</i>*</code></li>
        <li><code>**<i><b>strong</b></i>**</code></li>
        <li><code>- <i>list item</i></code></li>
        <li><code>><i>blockquote</i></code></li>
        <li><code>    <i>code</i></code></li>
        <li><code>------</code>horizontal line</li>
    </ul>

    <h4>W synthax</h4>
    <ul>
        <li><code>[[<i>page_id</i>]]</code> wiki link</li>
        <li><code>%TITLE%</code> print page title</li>
        <li><code>%DESCRIPTION%</code> print page description</li>
        <li><code>%THUMBNAIL%</code> print page thumbnail</li>
        <li><code>%DATE%</code> print date of page</li>
        <li><code>%TIME%</code> print time of page</li>
        <li><code>%SUMMARY?<i>option</i>=<i>value</i>%</code> generate summary</li>
        <li><code>%LIST?<i>option</i>=<i>value</i>%</code> generate list of page</li>
        <li><code>%MEDIA?<i>option</i>=<i>value</i>%</code> generate media list</li>
    </ul>

    <h4>BODY synthax</h4>
    <ul>
    <li><code>%<i>ELEMENT</i>?<i>option</i>=<i>value</i>%</code> include specified element</li>
    </ul>

    BODY don't support <strong>Markdown</strong> encoding.

    <h4>More infos</h4>

    <ul>
        <li><a href="<?= $this->url('info') ?>" target="_blank">📕 W Manual</a></li>
        <li><a href="https://commonmark.org/help/" target="_blank">📏 Markdown encoding</a></li>
        <li><a href="https://michelf.ca/projects/php-markdown/extra/" target="_blank">📐 Markdown Extra</a></li>
        <li><a href="https://codemirror.net/demo/search.html" target="_blank" rel="noopener noreferrer">🐒 Full Search/replace Doc</a></li>
    </ul>
