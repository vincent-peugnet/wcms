<?php

use Wcms\Model;

$this->layout('layout', ['title' => 'media', 'stylesheets' => [$css . 'home.css']]) ?>


<?php $this->start('page') ?>


<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'media', 'pagelist' => $pagelist]) ?>

<?php $this->insert('mediamenu', ['user' => $user, 'pathlist' => $pathlist, 'mediaopt' => $mediaopt, 'maxuploadsize' => $maxuploadsize]) ?>

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
                        <?= options(Model::MEDIA_SORTBY, $mediaopt->sortby()) ?>
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

    <h2>
        /<?= $mediaopt->dir() ?>
        <span class="right"><a href="<?= $mediaopt->getaddress() ?>&display=list" <?= $display === 'list' ? 'style="color: white"' : '' ?> >list</a> / <a href="<?= $mediaopt->getaddress() ?>&display=gallery"  <?= $display === 'gallery' ? 'style="color: white"' : '' ?>  >gallery</a></span>
    </h2>

    <div class="scroll">


    <?php if($display === 'gallery') { ?>


    <!-- ___________________ GALLERY _________________________ -->


    <ul id="gallery">
        <?php foreach ($medialist as $media) { ?>

        <li title="<?= $media->size('hr') ?> | <?= $media->uid('name') ?> | <?= $media->permissions() ?>">
            <div class="thumbnail">
            <label for="media_<?= $media->id() ?>">
                <?php
                switch ($media->type()) {
                    case 'image':
                        echo '<img src="' . $media->getfullpath() . '">';
                        break;
                    
                    case 'video':
                    case 'sound':
                        echo $media->getcode(true);
                        break;
                    
                    default:
                        echo $media->getsymbol(true);
                        break;
                }
                ?>
            </label>
            </div>
            
            <div class="meta">
                <input type="checkbox" name="id[]" value="<?= $media->getfulldir() ?>" form="mediaedit" id="media_<?= $media->id() ?>">
                <label for="media_<?= $media->id() ?>"><?= $media->id() ?></label>
                <a href="<?= $media->getfullpath() ?>" target="_blank"><i class="fa fa-external-link"></i></a>
                <input readonly class="code select-all" value="<?= $this->e($media->getcode()) ?>" />
            </div>
                
        </li>
        
        <?php } ?>
    </ul>


    <?php } else { ?>



    <!-- ___________________ L I S T _________________________ -->


        <table id="medialist">
        <tr>
            <th id="checkall">x</th>
            <th><a href="<?= $mediaopt->getsortbyadress('id') ?>">id</a></th>
            <th><a href="<?= $mediaopt->getsortbyadress('extension') ?>">ext</a></th>
            <th><a href="<?= $mediaopt->getsortbyadress('type') ?>">type</a></th>
            <th><a href="<?= $mediaopt->getsortbyadress('size') ?>">size</a></th>
            <th><a href="<?= $mediaopt->getsortbyadress('date') ?>">date</a></th>
            <th>user</th>
            <th>perms</th>
            <th>surface</th>
            <th>code</th>
        </tr>

        <?php
        foreach ($medialist as $media) {
            ?>
            <tr>
            <td><input type="checkbox" name="id[]" value="<?= $media->getfulldir() ?>" form="mediaedit" id="media_<?= $media->id() ?>"></td>
            <td>
                <details>
                    <summary>
                        <label for="newid"><?= $media->id() ?></label>
                    </summary>
                    <form action="<?= $this->url('mediarename') ?>" method="post">
                        <input type="hidden" name="route" value="<?= $mediaopt->getaddress() ?>">
                        <input type="hidden" name="path" value="<?= $media->path() ?>">
                        <input type="hidden" name="oldid" value="<?= $media->id() ?>">
                        <input type="hidden" name="oldextension" value="<?= $media->extension() ?>">
                        <input type="text" name="newid" value="<?= $media->id() ?>" id="newid" maxlength="<?= Wcms\Model::MAX_ID_LENGTH ?>" minlength="1" required>
                        <input type="text" name="newextension" value="<?= $media->extension() ?>" id="" style="width: 30px" maxlength="16" minlength="1" required>
                        <input type="submit" value="rename">
                    </form>
                </details>
            </td>    
            <td><?= $media->extension() ?></td>
            <td class="nowrap"><a href="<?= $media->getfullpath() ?>" target="_blank"><?= $media->type() == 'image' ? '<span class="thumbnail">' . $media->getsymbol() . '<img src="' . $media->getfullpath() . '"></span>' : $media->getsymbol() ?></a></td>
            <td class="nowrap"><?= $media->size('hr') ?></td>
            <td class="nowrap" title="<?= $media->date('dmy') ?> <?= $media->date('ptime') ?>"><?= $media->date('hrdi') ?></td>
            <td><?= $media->uid('name') ?></td>
            <td><code><?= $media->permissions() ?></code></td>
            <td><?= $media->surface() ?></td>
            <td><input readonly class="code select-all" value="<?= $this->e($media->getcode()) ?>" /></td>
            </tr>
            <?php
        }
        ?>

    <?php } ?>

    </div>

</table>

</div>
</section>

</main>

<?php if(!Wcms\Config::disablejavascript()) { ?>

<script src="<?= Wcms\Model::jspath() ?>media.bundle.js"></script>

<?php } ?>



<?php $this->stop('page') ?>