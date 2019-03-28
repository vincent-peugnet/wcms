<?php $this->layout('layout', ['title' => 'info', 'css' => $css . 'home.css']) ?>


<?php $this->start('page') ?>

<body>

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'info']) ?>


<main class="info">


<section>


<nav>
<h2>Manual Summary</h2>
<ul>
    <li><a href="#startup">Startup</a></li>
    <ul>

    </ul>
    <li><a href="#structure">Structure</a></li>
    <ul>
        <li><a href="#attributes">Page attributes</a></li>
        <li><a href="#database">Database</a></li>
    </ul>
    <li><a href="#editor">Editor</a></li>
    <ul>
        <li><a href="#elementsyntax">Elements syntax</a></li>
        <li><a href="#bodysyntax">Body syntax</a></li>
    </ul>
</ul>

</nav>


<article>

<h1>Info</h1>

<h2>Version</h2>

<?= $version ?>

<h2>Links</h2>

<ul>
<li><a href="https://github.com/vincent-peugnet/wcms" target="_blank">🐱‍👤 Github</a></li>
<li><a href="#manual">📕 Manual</a></li>
<li><a href="https://w-cms.top" target="_blank">🌵 Website</a></li>
</ul>

<h2>About</h2>

<h3>W-cms was made using these open sources and free components :</h3>

<ul>
<li><a href="https://github.com/jamesmoss/flywheel" target="_blank">🎡 James Moss's Flywheel Database</a> <i>as json noSQL flatfile database engine</i></li>
<li><a href="https://github.com/michelf/php-markdown" target="_blank">📝 Michel Fortin's Markdown Extra</a> <i>markdown library</i></li>
<li><a href="https://github.com/thephpleague/plates" target="_blank">🎨 Plates</a> <i>as templating engine</i></li>
<li><a href="https://github.com/dannyvankooten/AltoRouter">🐶 Alto Router</a> <i>as router engine</i></li>
</ul>

<h3>Special thanks to :</h3>

<a href="https://nicolas.club1.fr" target="_blank">🚲 Nicolas Peugnet</a>

</article>

<article id="manual">

<h2 id="manual">Manual</h2>

<h3 id="startup">Startup</h3>

<p>There is'nt a real start of anything when you're using W.</p>

<p>Just type an adress in your web browser, after your W folder. You should see an empty page, not existing yet. You can login to create one at this adress. If you are already logged in, you can juste type <code><i>page_id</i>/add</code>, this will directly create a page at this adress.</p>

<p>This <code><i>page_id</i></code> is the identifier of each page, you cant change it later, this is like a real solid adress, it cant move. It can only contain lowercase letters, numbers, hyphens or underscore charachters.</p>

<p>No visitor can see the full list of pages you've created. The only way to see the whole map of your project, is to be conected at least as an editor, and go to the "homepage", the root adress where W is instaled.</p>

<h3 id="structure">Structure</h3>

<h4>Pages and IDs</h4>

<p>The structure of website is very simple as there is no structure. It's you to link the pages together or not.</p>

<p>A page is defined by a unique <code>id</code>. As it's created, you can access it typing <code>.../<i>your_page_id</i>/</code> or without slash <code>.../<i>your_page_id</i></code></p>

<p>An ID can only contain lowercase letters, numbers, underscore and "-"</p>


<h4 id="attributes">Page attributes</h4>

<h5>Id</h5>

<p>Unique identifier, this is the url that point to your page</p>

<h5>title</h5>

<p>Page title, can use more complex characters than the ID element. It's printed in the explorer tab name. It's the official name of your page.</p>

<h5>Description</h5>

<p>The description attribute is a short informations about your page. It's used to create tooltip, when the mouse hover internal links.</p>

<h5>Tag(s)</h5>

<p>tags are used to create selections of pages. tags are one word only, sepparated by commas and whitespace.</p>

<h5>Privacy level</h5>

<p>Set the level of privacy of your page.
</br><strong>0</strong> -> public
</br><strong>1</strong> -> private <i>reserved to private readers</i>
</br><strong>2</strong> -> not published <i>Nobody but editors can see it.</i> </p>


<h5>Date & time</h5>

<p>You can manualy set a date and time for each pages.</p>

<h4 id="database">Database</h4>

<p>All this pages are stored in your database, as a file by page. You can access them in the <code>.../database/<i>name_of_your_store</i>/</code></p>











<h3 id="editor">Editor</h3>

<p>W use the fives basics html elements to store your content</p>

<ul>
<li>header</li>
<li>nav</li>
<li>aside</li>
<li>main</li>
<li>footer</li>
</ul>

You can use any of them, only one or all at the same time, as you prefer.

<h4 id="elementsyntax">Element syntax</h4>

<p>In any of the five html element you can use to store content, you can use the following syntax, that is specific to W, extending the Markdown syntax :</p>

<h5>quick internal link</h5>

<p>You can create internal link very quickly, only by using the id of your page. The link text will be remplaced by the title associated with this page.</p>

<blockquote>
    [<i>page_id</i>]
</blockquote>

<p>This will output :</p>

<blockquote>
&lt;a href="<i>page->url</i>" class="internal" title="<i>page->description</i>"&gt;<i>page->title</i>&lt;/a&gt;
</blockquote>

<h5>Title shortcut</h5>

<blockquote>
    %TITLE%
</blockquote>

<p>Will output the page <code>title</code> attribute. This can be usefull when templating, or including differents page element.</p>


<h5>Description shortcut</h5>

<blockquote>
    %DESCRIPTION%
</blockquote>

<p>As for the title, this will output the <code>description</code> attribute of the page</p>

<h5>Date shortcut</h5>

<blockquote>
    %DATE% %DATECREATION% %DATEMODIF%
</blockquote>

<p>There are tree dates attributes that can be printed. The <strong>date</strong> attribute, will return the date that can be manualy set in the editor. <strong>datecreation</strong> will return the date of the page creation. <strong>datemodif</strong> will output the last editing date.</p>


<h5>Automatic summary</h5>

<p>You can automatically generate summary, based on the page <code>&lt;h*&gt;</code> elements</p>

<blockquote>
    %SUMMARY%
</blockquote>

<p>This will generate a classic <code>&lt;ul&gt;</code> html list.</p>

<h5>Automatic list by tag</h5>

<p>You can create a html list of links pointing to all the pages using this tag.</p>

<blockquote>
    %TAG:<i>__tag__</i>%
</blockquote>

<p>Let's suppose we are in page3 and we have page2, page3, page5, using this tag, this will output :</p>

<blockquote>
&lt;ul id="<i>tag</i>"&gt;
</br>
&lt;li&gt;
</br>
&lt;a href="<i>2->url</i>" class="internal" title="<i>2->description</i>"&gt;<i>2->title</i>&lt;/a&gt;
</br>
&lt;/li&gt;
</br>
&lt;li&gt;
</br>
&lt;a href="<i>3->url</i>" class="internal actualpage" title="<i>3->description</i>"&gt;<i>3->title</i>&lt;/a&gt;
</br>
&lt;/li&gt;
</br>
&lt;li&gt;
</br>
&lt;a href="<i>5->url</i>" class="internal" title="<i>5->description</i>"&gt;<i>5->title</i>&lt;/a&gt;
</br>
&lt;/li&gt;
</br>
&lt;/ul&gt;
</blockquote>

<p>The list is ordered by the <code>date</code> attribute, that you can set manualy for each page. You may have noticed that the actual page (page 3), has been specified using <code>.actualpage</code> class. This can be usefull to create a menu and highlight the current page.</p>


<h5>Article separator</h5>

<p>You can use the <code>article</code> html element, by separating text using at least <code>====</code>. It is of course possible to use Markdown synthax inside those articles separators</p>

<blockquote>
====</br></br>
<i>some text</i></br></br>
======<i>important</i></br></br>
<i>this is a longer text</i></br></br>
======</br></br>
</blockquote>

<p>This will ouptut :</p>

<blockquote>
&lt;article&gt;</br>
<i>some text</i></br>
&lt;/article&gt;</br>
&lt;article id="<i>important</i>"&gt;</br>
<i>this is a longer textt</i></br>
&lt;/article&gt;</br>
</blockquote>


<p>As you may have noticed, there is also the possibility to add a custom <code>id</code> to any of the articles created that way</p>


<h5>Media list</h5>

<p>As it is too long adding all media of a folder one by one, you can just print the content of an entire folder using this method.</p>

<blockquote>
%MEDIA:<i>__directory__</i>
</blockquote>

<p>Just point to the good directory inside the media folder. This will output html list, creating image elements, audio players, video players, or just basic link, depending on the media type.</p>






<h4 id="bodysyntax">Body syntax</h4>




<h5>Basic including</h5>

<blockquote>%HTML_ELEMENT%</blockquote>


<p>This will include the matching html element from your page's content in your page body. If there is nothing in the corresponding element, it won't print anything. The name of the html element as to be UPPERCASE.</p>

<p>For example :</p>

<blockquote>
%ASIDE%
</br>
</br>
%MAIN%
</blockquote>

<p>Will output :</p>

<blockquote>
&lt;aside class="<i>page_id</i>"&gt;
</br>
__the content of your aside__
</br>

&lt;/aside&gt;
</br>
</br>
&lt;main class="<i>page_id</i>"&gt;
</br>
__the content of your main__
</br>

&lt;/main&gt;


</blockquote>

<p>You can also use one element multiple times.</p>

<h5>Advanced includings</h5>

<blockquote>
    %<i>HTML_ELEMENT</i>:<i>page_id</i>%
</blockquote>

<p>By doing this, you can include the <code>HTML_ELEMENT</code> of the page using this <code>page_id</code> id. You can even nest differents pages source by adding <code>page_id</code> separated by a dot, this would be like :</p>

<blockquote>
    %<i>HTML_ELEMENT</i>:<i>page1_id</i>+<i>page2_id</i>% 
</blockquote>

<p>And you can mix it with the original page content using <code>!</code> identifier</p>

<blockquote>
    %<i>HTML_ELEMENT</i>:<i>page3_id</i>+<i>!</i>%
</blockquote>

<p>This will output :</p>

<blockquote>

&lt;html_element class="<i>page3_id page_id</i>"&gt;
</br>
__content of page1's html element__
</br>
__content of this page html element__
</br>
&lt;/html_element&gt;



</blockquote>

</article>

</section>

</main>
</body>

<?php $this->stop('page') ?>