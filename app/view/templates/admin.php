<?php $this->layout('layout', ['title' => 'admin', 'css' => $css . 'home.css']) ?>


<?php $this->start('page') ?>

<body>

    <?php $this->insert('backtopbar', ['user' => $user]) ?>


<section class="admin">

    <form action="<?= $this->url('adminupdate') ?>" method="post">

    <h2>Passwords</h2>
    <label for="admin">Admin password</label>
    <input type="password" name="admin" id="admin" value="<?= Config::admin() ?>">
    <label for="editor">Editor password</label>
    <input type="password" name="editor" id="editor" value="<?= Config::editor() ?>">

    <h2>Page creation</h2>
    <label for="defaultart">Create new page based on an already existing one</label>
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
        <label for="defaultbody">Or edit default BODY element</label>
        <textarea name="defaultbody" id="defaultbody" cols="30" rows="10"><?= Config::defaultbody() ?></textarea>
        <?php
    }
    ?>


    <h2>Editing</h2>

    <label for="existnot">Text to show when a page does not exist yet</label>
    <input type="text" name="existnot" id="existnot" value="<?= Config::existnot() ?>">

    <label for="showeditmenu">Show editor menu in top right corner of pages</label>
    <input type="checkbox" name="showeditmenu" id="showeditmenu" <?= Config::showeditmenu() === true ? 'checked' : '' ?>>

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

    <input type="submit" value="submit">

    </form>


    

</section>
</body>

<?php $this->stop('page') ?>