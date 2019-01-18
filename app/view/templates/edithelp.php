

    <h4>update shortcut</h4>
    <kbd>CTRL</kbd> + <kbd>S</kbd>
    <h4>Markdown synthax</h4>
    <ul>
    <li><code>[<i>hello</i>](<i>PAGE_ID</i>)</code>link</li>
    <li><code>![<i>alt</i>](<i>imagepath</i>)</code>img</li>
    <li><code><<i>e@mail.net</i>></code></li>
    <li><code># <i>h</i>* <i>title</i></code></li>
    <li><code>*<i>emphasis</i>*</code></li>
    <li><code>**<i><b>strong</b></i>**</code></li>
    <li><code>- <i>list item</i></code></li>
    <li><code>><i>blockquote</i></code></li>
    <li><code>    <i>code</i></code></li>
    <li><code>------</code>horizontal line</li>
    </ul>
    <h4>W synthax</h4>
    <ul>
    <li><code>[<i>PAGE_ID</i>]</code>quick link</li>
    <li><code>%TITLE%</code>page title</li>
    <li><code>%DESCRIPTION%</code>page desc'</li>
    <li><code>%DATE%</code>date of page</li>
    <li><code>%TIME%</code>time of page</li>
    <li><code>%SUMMARY%</code>Summary</li>
    <li><code>===<i>id</i></code>article separator</li>
    <li><code>%TAG:<i>tag</i>%</code>page list by <i>tag</i></li>
    <li><code>%MEDIA:<i>dir</i>%</code>media list</li>
    <li><code>%LINK%<i>text</i>%LINK%</code>auto link</li>
    </ul>

    <h4>BODY synthax</h4>
    <ul>
    <li><code>%<i>ELEMENT</i>%</code>invoke page element</li>
    <li><code>%<i>ELEMENT</i>:<i>page_id</i>%</code>invoke element of specific page</li>
    <li>You cant use Markdown in the BODY</li>
    </ul>


    <h4>More infos</h4>

    <ul>
    <li><a href="<?= $this->url('info') ?>" target="_blank">📕 W Manual</a></li>
    <li><a href="https://daringfireball.net/projects/markdown/syntax" target="_blank">📏 Markdown encoding</a></li>
    <li><a href="https://michelf.ca/projects/php-markdown/extra/" target="_blank">📐 Markdown Extra</a></li>
    </ul>
