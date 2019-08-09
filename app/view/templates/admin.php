<?php $this->layout('layout', ['title' => 'admin', 'css' => $css . 'home.css']) ?>


<?php $this->start('page') ?>

<body>

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'admin']) ?>


    <main class="admin">

        <section>

            <form action="<?= $this->url('adminupdate') ?>" method="post">


                <article>

                    <h1>Administration</h1>

                    <input type="submit" value="Update configuration">
                </article>

                <article>

                    <h2>Home page</h2>

                    <p>Here you can set the home-page view for visitors.</p>

                    <div class="radio">
                        <input type="radio" name="homepage" value="default" id="default" <?= Config::homepage() === 'default' ? 'checked' : '' ?>>
                        <label for="default">default</label>
                    </div>

                    <div class="radio">
                        <input type="radio" name="homepage" value="search" id="searchbar" <?= Config::homepage() === 'search' ? 'checked' : '' ?>>
                        <label for="searchbar">search bar</label>
                    </div>

                    <div class="radio">
                        <input type="radio" name="homepage" value="redirect" id="redirect" <?= Config::homepage() === 'redirect' ? 'checked' : '' ?>>
                        <label for="redirect">redirect to page</label>
                    </div>

                    <select name="homeredirect" id="homeredirect">
                        <option value="" <?= Config::homeredirect() === null ? 'selected' : '' ?>>--select page to redirect--</option>

                        <?php
                        foreach ($artlist as $art) {
                            ?>
                            <option value="<?= $art ?>" <?= Config::homeredirect() === $art ? 'selected' : '' ?>><?= $art ?></option>
                        <?php
                        }


                        ?>
                    </select>

                </article>

                <article>

                    <h2>Page creation</h2>

                    <p>What really happend when you create a new page</p>

                    <label for="defaultprivacy">Default privacy</label>
                    <select name="defaultprivacy" id="defaultprivacy">
                        <option value="0" <?= Config::defaultprivacy() == 0 ? 'selected' : '' ?>>public</option>
                        <option value="1" <?= Config::defaultprivacy() == 1 ? 'selected' : '' ?>>private</option>
                        <option value="2" <?= Config::defaultprivacy() == 2 ? 'selected' : '' ?>>not published</option>
                    </select>





                    <label for="defaultart">Or, create new page BODY based on an already existing one</label>
                    <select name="defaultart" id="defaultart">
                        <option value="" <?= Config::defaultart() === '' || !$defaultartexist ? 'selected' : '' ?>>--use default BODY element--</option>
                        <?php
                        foreach ($artlist as $art) {
                            ?>
                            <option value="<?= $art ?>" <?= Config::defaultart() === $art ? 'selected' : '' ?>><?= $art ?></option>
                        <?php    }
                        ?>
                    </select>

                    <?php
                    if (empty(!$defaultartexist || Config::defaultart())) {
                        ?>
                        <label for="defaultbody">Edit default BODY element</label>
                        <textarea name="defaultbody" id="defaultbody" cols="30" rows="10"><?= Config::defaultbody() ?></textarea>
                    <?php
                    }
                    ?>

                </article>

                <article>



                    <h2>Alert pages</h2>

                    <h4>Common options</h4>

                    <label for="alerttitle">H1 Title</label>
                    <input type="text" name="alerttitle" id="alerttitle" value="<?= Config::alerttitle() ?>">


                    <label for="alertlink">Link to this page (for visitors)</label>
                    <select name="alertlink" id="alertlink">
                        <option value="" <?= empty(Config::alertlink()) ? 'selected' : '' ?>>--No link--</option>
                        <?php
                        foreach ($artlist as $art) {
                            ?>
                            <option value="<?= $art ?>" <?= Config::alertlink() === $art ? 'selected' : '' ?>><?= $art ?></option>
                        <?php    }
                        ?>
                    </select>


                    <label for="alertlinktext">Link text</label>
                    <input type="text" name="alertlinktext" id="alertlinktext" value="<?= Config::alertlinktext() ?>">



                    <h4>Un-existing</h4>

                    <label for="existnot">Text to show when a page does not exist yet.</label>
                    <i>This will also be shown as a tooltip over links.</i>
                    <input type="text" name="existnot" id="existnot" value="<?= Config::existnot() ?>">

                    <div class="checkbox">
                        <input type="hidden" name="existnotpass" value="0">
                        <input type="checkbox" name="existnotpass" id="existnotpass" value="1" <?= Config::existnotpass() ? 'checked' : '' ?>>
                        <label for="existnotpass">Ask for password</label>
                    </div>

                    <h4>Private</h4>

                    <label for="private">Text to show when a page is private.</label>
                    <input type="text" name="private" id="private" value="<?= Config::private() ?>">

                    <div class="checkbox">
                        <input type="hidden" name="privatepass" value="0">
                        <input type="checkbox" name="privatepass" id="privatepass" value="1" <?= Config::privatepass() ? 'checked' : '' ?>>
                        <label for="privatepass">Ask for password</label>
                    </div>

                    <h4>Not published</h4>

                    <label for="notpublished">Text to show when a page is not published.</label>
                    <input type="text" name="notpublished" id="notpublished" value="<?= Config::notpublished() ?>">

                    <div class="checkbox">
                        <input type="hidden" name="notpublishedpass" value="0">
                        <input type="checkbox" name="notpublishedpass" id="notpublishedpass" value="1" <?= Config::notpublishedpass() ? 'checked' : '' ?>>
                        <label for="notpublishedpass">Ask for password</label>
                    </div>

                    <h4>CSS</h4>

                    <div class="checkbox">
                        <input type="hidden" name="alertcss" value="0">
                        <input type="checkbox" name="alertcss" id="alertcss" value="1" <?= Config::alertcss() ? 'checked' : '' ?>>
                        <label for="alertcss">Use global.css for those page as well</label>
                    </div>

                    <p>
                        <i>You can use <code>body.alert</code> class to specify style.</i>
                    </p>

                </article>
                
                
                <article>
                    

                    <h2>Render</h2>

                    <div class="checkbox">
                        <input type="hidden" name="reccursiverender" value="0">
                        <input type="checkbox" name="reccursiverender" id="reccursiverender" value="1" <?= Config::reccursiverender() ? 'checked' : '' ?>>
                        <label for="reccursiverender">Reccursive render</label>
                    </div>


                    <h4>Links</h4>

                    <div class="checkbox">
                        <input type="hidden" name="externallinkblank" value="0">
                        <input type="checkbox" name="externallinkblank" id="externallinkblank" value="1" <?= Config::externallinkblank() ? 'checked' : '' ?>>
                        <label for="externallinkblank">Open external links in new tab</label>
                    </div>

                    <div class="checkbox">
                        <input type="hidden" name="internallinkblank" value="0">
                        <input type="checkbox" name="internallinkblank" id="internallinkblank" value="1" <?= Config::internallinkblank() ? 'checked' : '' ?>>
                        <label for="internallinkblank">Open internal links in new tab</label>
                    </div>

                    <i>(This modifications need re-rendering)</i>

                    <h4>Edit quick menu</h4>

                    <div class="checkbox">
                        <input type="hidden" name="showeditmenu" value="false">
                        <input type="checkbox" name="showeditmenu" id="showeditmenu" value="true" <?= Config::showeditmenu() === true ? 'checked' : '' ?>>
                        <label for="showeditmenu">Show editor menu in top right corner of pages</label>
                    </div>

                    <?php
                    if (Config::showeditmenu() === true) {
                        ?>
                        <label for="editsymbol">Symbol</label>
                        <select name="editsymbol" id="editsymbol">
                            <?php
                            foreach (Model::EDIT_SYMBOLS as $symbol) {
                                ?>
                                <option value="<?= $symbol ?>" <?= Config::editsymbol() === $symbol ? 'selected' : '' ?>><?= $symbol ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    <?php
                    }
                    ?>

                </article>





                <article>

                    <h2>CSS</h2>

                    <label for="globalcss">Edit global css that will apply to every pages</label>
                    <textarea name="globalcss" id="globalcss" cols="30" rows="10"><?= $globalcss ?></textarea>

                    <label for="defaultfavicon">Default favicon</label>
                    <select name="defaultfavicon" id="defaultfavicon">
                        <option value="">--no favicon--</option>
                        <?php
                        foreach ($faviconlist as $favicon) {
                            ?>
                            <option value="<?= $favicon ?>" <?= Config::defaultfavicon() === $favicon ? 'selected' : '' ?>><?= $favicon ?></option>
                        <?php
                        }
                        ?>
                    </select>

                </article>







                <article>

                    <h2>Interface</h2>

                    <p>Set interface Style</p>

                    <select name="interfacecss" id="interfacecss">
                        <option value="null">--default interface style---</option>
                        <?php
                        foreach ($interfacecsslist as $interfacecss) {
                            ?>
                            <option value="<?= $interfacecss ?>" <?= $interfacecss === Config::interfacecss() ? 'selected' : '' ?>><?= $interfacecss ?></option>
                        <?php
                        }
                        ?>
                    </select>

                </article>





                <article>

                    <h2>Tracking</h2>

                    <label for="analytics">Google analytics Tracking ID</label>
                    <input type="text" name="analytics" id="analytics" value="<?= Config::analytics() ?>">

                    <i>(Need rendering to work)</i>

                </article>





                <article>
                    <input type="submit" value="Update configuration">
                </article>



            </form>


        </section>

    </main>
</body>

<?php $this->stop('page') ?>