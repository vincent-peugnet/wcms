<?php $this->layout('layout', ['title' => 'admin', 'css' => $css . 'home.css']) ?>


<?php $this->start('page') ?>

<body>

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'admin']) ?>


<main class="admin">

    <h1>Administration</h1>

    <form action="<?= $this->url('adminupdate') ?>" method="post">

    <input type="submit" value="Update configuration">


    <h2>Page creation</h2>
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


    <h2>Editing</h2>

    <label for="existnot">Text to show when a page does not exist yet</label>
    <input type="text" name="existnot" id="existnot" value="<?= Config::existnot() ?>">

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


    <h2>Tracking</h2>

    <label for="analytics">Google analytics Tracking ID</label>
    <input type="text" name="analytics" id="analytics" value="<?= Config::analytics() ?>">


    <input type="submit" value="Update configuration">

    </form>


    

</main>
</body>

<?php $this->stop('page') ?>