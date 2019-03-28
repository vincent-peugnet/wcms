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
    <input type="radio" name="homepage" value="redirect" id="redirect"  <?= Config::homepage() === 'redirect' ? 'checked' : '' ?>>
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

    <label for="defaultprivacy">Default privacy</label>
    <select name="defaultprivacy" id="defaultprivacy">
    <option value="0" <?= Config::defaultprivacy() == 0 ? 'selected' : '' ?>>public</option>
    <option value="1" <?= Config::defaultprivacy() == 1 ? 'selected' : '' ?>>private</option>
    <option value="2" <?= Config::defaultprivacy() == 2 ? 'selected' : '' ?>>not published</option>
    </select>





    <label for="defaultart">Create new page BODY based on an already existing one</label>
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
    if(empty(!$defaultartexist || Config::defaultart())) {
        ?>
        <label for="defaultbody">Edit default BODY element</label>
        <textarea name="defaultbody" id="defaultbody" cols="30" rows="10"><?= Config::defaultbody() ?></textarea>
        <?php
    }
    ?>

    </article>

    <article>



    <h2>Editing</h2>

    <label for="existnot">Text to show when a page does not exist yet</label>
    <input type="text" name="existnot" id="existnot" value="<?= Config::existnot() ?>">


    <h4>Render</h4>

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
    <input type="checkbox" name="showeditmenu" id="showeditmenu" <?= Config::showeditmenu() === true ? 'checked' : '' ?>>
    <label for="showeditmenu">Show editor menu in top right corner of pages</label>
    </div>

    <?php
    if(Config::showeditmenu() === true) {
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