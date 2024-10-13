<?php

$this->layout('layout', ['title' => 'admin', 'stylesheets' => [$css . 'back.css', $css . 'admin.css']]) ?>


<?php $this->start('page') ?>

<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'admin', 'pagelist' => $pagelist]) ?>


<main class="admin">

    <nav class="admin">

        <div class="block">
            <h1>Administration</h1>

            <div class="scroll">
                <ul>
                    <li><a href="#home-page">Home page</a></li>
                    <li><a href="#page-creation">Page creation</a></li>
                    <li><a href="#alert-pages">Alert Pages</a></li>
                    <li><a href="#render">Render</a></li>
                    <li><a href="#style">Style</a></li>
                    <li><a href="#interface">Interface</a></li>
                </ul>

                <form action="<?= $this->url('adminupdate') ?>" method="post" id="admin">
                    
                    <button type="submit">
                        <i class="fa fa-save"></i>
                        <span>Save configuration</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <section class="admin">

        <div class="block">

            <h1>configuration</h1>

            <div class="scroll">


                <h2 id="home-page">Home page</h2>

                <p>
                    By default, W has no home page for visitors without an account.
                    But if you wish, you can define a page to which visitors who are not logged in will be redirected.
                </p>

                <div class="radio">
                    <input type="radio" name="homepage" value="default" id="default" <?= Wcms\Config::homepage() === 'default' ? 'checked' : '' ?> form="admin">
                    <label for="default">default</label>
                </div>

                <div class="radio">
                    <input type="radio" name="homepage" value="redirect" id="redirect" <?= Wcms\Config::homepage() === 'redirect' ? 'checked' : '' ?> form="admin">
                    <label for="redirect">redirect to page</label>
                </div>

                <select name="homeredirect" id="homeredirect" form="admin">
                    <option value="" <?= Wcms\Config::homeredirect() === null ? 'selected' : '' ?>>--select page to redirect--</option>

                    <?php
                    foreach ($pagelist as $page) {
                    ?>
                        <option value="<?= $page ?>" <?= Wcms\Config::homeredirect() === $page ? 'selected' : '' ?>><?= $page ?></option>
                    <?php
                    }


                    ?>
                </select>


                <h2 id="page-creation">Page creation</h2>

                <p>What really happend when you create a new page</p>
                
                <h3>Privacy of new pages</h3>

                <label for="defaultprivacy">Default privacy</label>
                <select name="defaultprivacy" id="defaultprivacy" form="admin">
                    <option value="0" <?= Wcms\Config::defaultprivacy() == 0 ? 'selected' : '' ?>>public</option>
                    <option value="1" <?= Wcms\Config::defaultprivacy() == 1 ? 'selected' : '' ?>>private</option>
                    <option value="2" <?= Wcms\Config::defaultprivacy() == 2 ? 'selected' : '' ?>>not published</option>
                </select>

                <h3>Page version</h3>

                <p>Choose W page version you want to use when a new page is created.</p>

                <label for="pageversion">Select page version</label>
                <select name="pageversion" id="pageversion" form="admin">
                    <?= options(Wcms\Page::VERSIONS, Wcms\Config::pageversion()) ?>
                </select>

                <h3>Default BODY</h3>

                <?php $defaultbody = 'defaultv' . Wcms\Config::pageversion() . 'body' ?>
                <label for="defaultbody">Edit default page V<?= Wcms\Config::pageversion() ?> BODY content</label>
                <textarea name="<?= $defaultbody ?>" id="defaultbody" cols="30" rows="10" form="admin"><?= Wcms\Config::defaultbody() ?></textarea>



                <h2 id="alert-pages">Alert pages</h2>

                <p>Set the style and text to show when a page does not exist, or when a visitor don't have access to it.</p>

                <h4>Common options</h4>

                <label for="alerttitle">H1 Title</label>
                <input type="text" name="alerttitle" id="alerttitle" value="<?= Wcms\Config::alerttitle() ?>" form="admin">


                <label for="alertlink">Link to this page (for visitors)</label>
                <select name="alertlink" id="alertlink" form="admin">
                    <option value="" <?= empty(Wcms\Config::alertlink()) ? 'selected' : '' ?>>--No link--</option>
                    <?php
                    foreach ($pagelist as $page) {
                    ?>
                        <option value="<?= $page ?>" <?= Wcms\Config::alertlink() === $page ? 'selected' : '' ?>><?= $page ?></option>
                    <?php    }
                    ?>
                </select>


                <label for="alertlinktext">Link text</label>
                <input type="text" name="alertlinktext" id="alertlinktext" value="<?= Wcms\Config::alertlinktext() ?>" form="admin">



                <h4>Un-existing</h4>

                <label for="existnot">Text to show when a page does not exist yet.</label>
                <i>This will also be shown as a tooltip over links.</i>
                <input type="text" name="existnot" id="existnot" value="<?= Wcms\Config::existnot() ?>" form="admin">

                <div class="checkbox">
                    <input type="hidden" name="existnotpass" value="0" form="admin">
                    <input type="checkbox" name="existnotpass" id="existnotpass" value="1" <?= Wcms\Config::existnotpass() ? 'checked' : '' ?> form="admin">
                    <label for="existnotpass">Ask for password</label>
                </div>

                <h4>Private</h4>

                <label for="private">Text to show when a page is private.</label>
                <input type="text" name="private" id="private" value="<?= Wcms\Config::private() ?>" form="admin">

                <div class="checkbox">
                    <input type="hidden" name="privatepass" value="0" form="admin">
                    <input type="checkbox" name="privatepass" id="privatepass" value="1" <?= Wcms\Config::privatepass() ? 'checked' : '' ?> form="admin">
                    <label for="privatepass">Ask for password</label>
                </div>

                <h4>Not published</h4>

                <label for="notpublished">Text to show when a page is not published.</label>
                <input type="text" name="notpublished" id="notpublished" value="<?= Wcms\Config::notpublished() ?>" form="admin">

                <div class="checkbox">
                    <input type="hidden" name="notpublishedpass" value="0" form="admin">
                    <input type="checkbox" name="notpublishedpass" id="notpublishedpass" value="1" <?= Wcms\Config::notpublishedpass() ? 'checked' : '' ?> form="admin">
                    <label for="notpublishedpass">Ask for password</label>
                </div>

                <h4>CSS</h4>

                <div class="checkbox">
                    <input type="hidden" name="alertcss" value="0" form="admin">
                    <input type="checkbox" name="alertcss" id="alertcss" value="1" <?= Wcms\Config::alertcss() ? 'checked' : '' ?> form="admin">
                    <label for="alertcss">Use global.css for those page as well</label>
                </div>

                <p>
                    <i>You can use <code>body.alert</code> class to specify style.</i>
                </p>



                <h2 id="render">Render</h2>

                <h4>rendering details</h4>

                <div class="checkbox">
                    <input type="hidden" name="deletelinktocache" value="0" form="admin">
                    <input type="checkbox" name="deletelinktocache" id="deletelinktocache" value="1" <?= Wcms\Config::deletelinktocache() ? 'checked' : '' ?> form="admin">
                    <label for="deletelinktocache">invalidates the rendering of linked pages when updating</label>
                    <p>
                        When a page is modified, this may affect the rendering of other pages linked to it.
                        The pages to which it points have a strong possibility of needing to be updated too.
                        This option will invalidate their rendering each time the page pointing to them is updated.
                        They will therefore be re-rendered the next time they are viewed.
                    </p>
                </div>

                <h4>base page language</h4>

                <label for="lang">Default language</label>
                <input type="text" name="lang" id="lang" value="<?= Wcms\Config::lang() ?>" form="admin" minlength="<?= Wcms\Config::LANG_MIN ?>" maxlength="<?= Wcms\Config::LANG_MAX ?>" required>

                <p>
                    If the page language is not specified in metadatas, then this default will be used.
                </p>

                <h4>title</h4>

                <label for="suffix">suffix</label>
                <input type="text" name="suffix" id="suffix" value="<?= Wcms\Config::suffix() ?>" form="admin" maxlength="<?= Wcms\Config::SUFFIX_MAX ?>">
                <p>
                    This add a suffix to the title of all your pages.
                </p>

                <h4>Links</h4>

                <div class="checkbox">
                    <input type="hidden" name="externallinkblank" value="0" form="admin">
                    <input type="checkbox" name="externallinkblank" id="externallinkblank" value="1" <?= Wcms\Config::externallinkblank() ? 'checked' : '' ?> form="admin">
                    <label for="externallinkblank">Open external links in new tab</label>
                </div>

                <div class="checkbox">
                    <input type="hidden" name="internallinkblank" value="0" form="admin">
                    <input type="checkbox" name="internallinkblank" id="internallinkblank" value="1" <?= Wcms\Config::internallinkblank() ? 'checked' : '' ?> form="admin">
                    <label for="internallinkblank">Open internal links in new tab</label>
                </div>

                <h4>Markdown Parser</h4>

                <div class="checkbox">
                    <input type="hidden" name="markdownhardwrap" value="0" form="admin">
                    <input type="checkbox" name="markdownhardwrap" id="markdownhardwrap" value="1" <?= Wcms\Config::markdownhardwrap() ? 'checked' : '' ?> form="admin">
                    <label for="markdownhardwrap" title="When activated, single line break will be rendered as &lt;br/&gt;" >Render soft-linebreaks as &lt;br/&gt;</label>
                </div>

                <h4>Url linker</h4>

                <div class="checkbox">
                    <input type="hidden" name="urllinker" value="0" form="admin">
                    <input type="checkbox" name="urllinker" id="urllinker" value="1" <?= Wcms\Config::urllinker() ? 'checked' : '' ?> form="admin">
                    <label for="urllinker">transform plain text URLs to links</label>
                </div>

                <p>This can be overide individualy for each element using render options. See <a href="<?= $this->url('info', [], '#url-linker') ?>">📖 manual section</a> for more infos.</p>


                <h4>HTML tags (page V1)</h4>

                <div class="checkbox">
                    <input type="hidden" name="htmltag" value="0" form="admin">
                    <input type="checkbox" name="htmltag" id="htmltag" value="1" <?= Wcms\Config::htmltag() ? 'checked' : '' ?> form="admin">
                    <label for="htmltag">Print HTML tags around V1 page's contents</label>
                </div>

                <p>This can be overide individualy for each element using render options. See <a href="<?= $this->url('info', [], '#html-tags') ?>">📖 manual section</a> for more infos.</p>

                <h4>Lazy load images</h4>

                <div class="checkbox">
                    <input type="hidden" name="lazyloadimg" value="0" form="admin">
                    <input type="checkbox" name="lazyloadimg" id="lazyloadimg" value="1" <?= Wcms\Config::lazyloadimg() ? 'checked' : '' ?> form="admin">
                    <label for="lazyloadimg">Add <em>loading="lazy"</em> attribute to images</label>
                </div>

                <p>
                    <i>(Thoses modifications need re-rendering)</i>
                </p>


                <h2 id="style">Style</h2>

                <h4>Global CSS</h4>

                <p>
                    Global CSS will be loaded with every pages.
                </p>
                <textarea name="globalcss" id="globalcss" cols="30" rows="30" form="admin"><?= $globalcss ?></textarea>

                <h4>Favicon</h4>

                <label for="defaultfavicon">Default favicon</label>
                <select name="defaultfavicon" id="defaultfavicon" form="admin">
                    <option value="">--no favicon--</option>
                    <?php
                    foreach ($faviconlist as $favicon) {
                    ?>
                        <option value="<?= $favicon ?>" <?= Wcms\Config::defaultfavicon() === $favicon ? 'selected' : '' ?>><?= $favicon ?></option>
                    <?php
                    }
                    ?>
                </select>

                <h4>Thumbnail</h4>

                <label for="defaultthumbnail">Default thumbnail</label>
                <select name="defaultthumbnail" id="defaultthumbnail" form="admin">
                    <option value="">--no thumbnail--</option>
                    <?php
                    foreach ($thumbnaillist as $thumbnail) {
                    ?>
                        <option value="<?= $thumbnail ?>" <?= Wcms\Config::defaultthumbnail() === $thumbnail ? 'selected' : '' ?>><?= $thumbnail ?></option>
                    <?php } ?>
                </select>

                <h2 id="interface">Interface</h2>

                <h4>Theme</h4>


                <label for="theme">select interface theme</label>
                <select name="theme" id="theme" form="admin">
                    <?php
                    foreach ($themes as $theme) {
                    ?>
                        <option value="<?= $theme ?>" <?= $theme === Wcms\Config::theme() ? 'selected' : '' ?>><?= $theme ?></option>
                    <?php
                    }
                    ?>
                </select>

                <p>
                    See <a href="<?= $this->url('info', [], '#theming') ?>">📖 manual section</a> for more infos.
                </p>

                <h4>Javascript</h4>

                <div class="checkbox">
                    <input type="hidden" name="disablejavascript" value="0" form="admin">
                    <input type="checkbox" name="disablejavascript" id="disablejavascript" value="1" <?= Wcms\Config::disablejavascript() ? 'checked' : '' ?> form="admin">
                    <label for="disablejavascript">Disable javascript</label>
                </div>

                <p>
                    Disables javascript in the user interface.
                    Syntax highlighting, depend on it and will therefore not be displayed.
                    This also reduces comfort a little, but full functionality is retained.
                </p>



            </div>

        </div>


    </section>

    <section id="databases">
        <div class="block">
            <h1>Databases</h1>
            <div class="scroll">

            <form action="<?= $this->url('admindatabase') ?>" method="post">

                
                <table id="dirlsit">
                    <tr><th>using</th><th>databases</th><th>pages</th></tr>
                    
                    <?php basictree($pagesdbtree, 'pages', 0, '', DIRECTORY_SEPARATOR . Wcms\Config::pagetable()); ?>
                </table>

                <input type="hidden" name="action" value="select">
                <input type="submit" value="select" name="change database">

            </form>

            <h4>Duplicate Database</h4>

            <form action="<?= $this->url('admindatabase') ?>" method="post">

                <label for="dbsrc">Database to duplicate</label>
                <select name="dbsrc" id="dbsrc">
                    <?php
                    foreach ($pagesdblist as $db) {
                        ?>
                        <option value="<?= $db ?>" <?= $db === Wcms\Config::pagetable() ? 'selected' : '' ?>><?= $db ?></option>
                        <?php
                    }
                    ?>
                </select>

                <label for="duplicate">New name</label>
                <input type="text" name="dbtarget" id="duplicate" value="" required>
                <input type="submit" name="action" value="duplicate">
            </form>


            </div>
        </div>
    </section>

</main>

<?php $this->stop('page') ?>
