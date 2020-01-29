<?php

use Wcms\Model;

$this->layout('layout', ['title' => 'media', 'stylesheets' => [$css . 'home.css']]) ?>


<?php $this->start('page') ?>

<body>

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'media', 'pagelist' => $pagelist]) ?>

    <?php $this->insert('mediamenu', ['user' => $user, 'pathlist' => $pathlist, 'mediaopt' => $mediaopt]) ?>

<main class="media">


<nav class="media">
    <div class="block">
    <h2>Explorer</h2>
        <div class="scroll">
            <table id="dirlsit">
            <tr><th>folder</th><th>files</th></tr>

            <?php


            treecount($dirlist, 'media', 0, 'media', $mediaopt->dir(), $mediaopt);

            ?>

            </table>
        </div>
        
    </div>
</nav>

<div id="fildter">
    <div class="block">
        <h2>filter</h2>
        <div class="scroll">
            <form action="" method="get">
                <fieldset>
                    <legend>Type</legend>
                    <?= checkboxes('type', Model::mediatypes(), $mediaopt->type()) ?>
                </fieldset>
                <fieldset>
                    <legend>Sort</legend>
                    <select name="sortby" id="sortby">
                        <option value="id" <?= $mediaopt->sortby() === 'id' ? 'selected' : '' ?>>id</option>
                        <option value="type" <?= $mediaopt->sortby() === 'type' ? 'selected' : '' ?>>type</option>
                        <option value="size" <?= $mediaopt->sortby() === 'size' ? 'selected' : '' ?>>size</option>
                    </select>
                    </br>
                    <input type="radio" name="order" id="asc" value="1" <?= $mediaopt->order() == 1 ? 'checked' : '' ?>><label for="asc">ascending</label>
                    </br>
                    <input type="radio" name="order" id="desc" value="-1" <?= $mediaopt->order() == -1 ? 'checked' : '' ?>><label for="desc">descending</label>
                    </br>
                </fieldset>
                <input type="hidden" name="path" value="<?= $mediaopt->path() ?>">
                <input type="submit" value="filter">
            </form>
        </div>
    </div>
        </div>



<section>
    <div class="block">

    <h2>/<?= $mediaopt->dir() ?></h2>

    <div class="scroll">

        <table id="medialist">
        <tr>
            <th id="checkall">x</th>
            <th><a href="<?= $mediaopt->getsortbyadress('id') ?>">id</a></th>
            <th>ext</th>
            <th><a href="<?= $mediaopt->getsortbyadress('type') ?>">type</a></th>
            <th><a href="<?= $mediaopt->getsortbyadress('size') ?>">size</a></th>
            <th>width</th>
            <th>height</th>
            <th>lengh</th>
            <th>code</th>
        </tr>

        <?php
        foreach ($medialist as $media) {
            ?>
            <tr>
            <td><input type="checkbox" name="id[]" value="<?= $media->getfulldir() ?>" form="mediaedit" id="media_<?= $media->id() ?>"></td>
            <td><label for="media_<?= $media->id() ?>"><?= $media->id() ?></label></td>    
            <td><?= $media->extension() ?></td>
            <td class="nowrap"><a href="<?= $media->getfullpath() ?>" target="_blank"><?= $media->type() == 'image' ? '<span class="thumbnail">' . $media->getsymbol() . '<img src="' . $media->getfullpath() . '"></span>' : $media->getsymbol() ?></a></td>
            <td class="nowrap"><?= $media->size('hr') ?></td>
            <td><?= $media->width() ?></td>
            <td><?= $media->height() ?></td>
            <td><?= $media->length() ?></td>
            <td class="code"><code><?= $media->getcode() ?></code></td>
            </tr>
            <?php
        }
        ?>

    </div>

</table>

</div>
</section>

</main>

<script src="<?= Wcms\Model::jspath() ?>media.bundle.js"></script>

</body>

<?php $this->stop('page') ?>