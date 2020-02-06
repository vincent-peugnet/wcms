<?php $this->layout('layout', ['title' => 'admin', 'stylesheets' => [$css . 'home.css']]) ?>


<?php $this->start('page') ?>

<body>

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
                        <li><a href="#css">CSS</a></li>
                        <li><a href="#interface">Interface</a></li>
                        <li><a href="#tracking">Tracking</a></li>
                    </ul>

                    <form action="<?= $this->url('adminupdate') ?>" method="post" id="admin">
                        <input type="submit" value="Update configuration">
                    </form>
                </div>
            </div>
        </nav>

        <section class="admin">

            <div class="block">

                <h1>configuration</h1>

                <div class="scroll">


                    <h2 id="home-page">Home page</h2>

                    <p>Here you can set the home-page view for visitors.</p>

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

                    <label for="defaultprivacy">Default privacy</label>
                    <select name="defaultprivacy" id="defaultprivacy" form="admin">
                        <option value="0" <?= Wcms\Config::defaultprivacy() == 0 ? 'selected' : '' ?>>public</option>
                        <option value="1" <?= Wcms\Config::defaultprivacy() == 1 ? 'selected' : '' ?>>private</option>
                        <option value="2" <?= Wcms\Config::defaultprivacy() == 2 ? 'selected' : '' ?>>not published</option>
                    </select>





                    <label for="defaultpage">Or, create new page BODY based on an already existing one</label>
                    <select name="defaultpage" id="defaultpage" form="admin">
                        <option value="" <?= Wcms\Config::defaultpage() === '' || !$defaultpageexist ? 'selected' : '' ?>>--use default BODY element--</option>
                        <?php
                        foreach ($pagelist as $page) {
                        ?>
                            <option value="<?= $page ?>" <?= Wcms\Config::defaultpage() === $page ? 'selected' : '' ?>><?= $page ?></option>
                        <?php    }
                        ?>
                    </select>

                    <?php
                    if (empty(!$defaultpageexist || Wcms\Config::defaultpage())) {
                    ?>
                        <label for="defaultbody">Edit default BODY element</label>
                        <textarea name="defaultbody" id="defaultbody" cols="30" rows="10" form="admin"><?= Wcms\Config::defaultbody() ?></textarea>
                    <?php
                    }
                    ?>


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

                    <div class="checkbox">
                        <input type="hidden" name="reccursiverender" value="0" form="admin">
                        <input type="checkbox" name="reccursiverender" id="reccursiverender" value="1" <?= Wcms\Config::reccursiverender() ? 'checked' : '' ?> form="admin">
                        <label for="reccursiverender">Reccursive render</label>
                    </div>


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

                    <i>(This modifications need re-rendering)</i>



                    <h2 id="css">CSS</h2>

                    <label for="globalcss">Edit global css that will apply to every pages</label>
                    <textarea name="globalcss" id="globalcss" cols="30" rows="30" form="admin"><?= $globalcss ?></textarea>

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

                    <p>Set interface Style</p>

                    <select name="interfacecss" id="interfacecss" form="admin">
                        <option value="null">--default interface style---</option>
                        <?php
                        foreach ($interfacecsslist as $interfacecss) {
                        ?>
                            <option value="<?= $interfacecss ?>" <?= $interfacecss === Wcms\Config::interfacecss() ? 'selected' : '' ?>><?= $interfacecss ?></option>
                        <?php
                        }
                        ?>
                    </select>


                    <h2 id="tracking">Tracking</h2>

                    <label for="analytics">Google analytics Tracking ID</label>
                    <input type="text" name="analytics" id="analytics" value="<?= Wcms\Config::analytics() ?>" form="admin">

                    <i>(Need rendering to work)</i>



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
</body>

<?php $this->stop('page') ?>