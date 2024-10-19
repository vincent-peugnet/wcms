<?php


$this->layout('layout', ['title' => 'media', 'stylesheets' => [$css . 'back.css', $css . 'media.css', $cssfont]]) ?>


<?php $this->start('page') ?>


<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'media', 'pagelist' => $pagelist]) ?>

<?php $this->insert('mediamenu', ['user' => $user, 'pathlist' => $pathlist, 'mediaopt' => $mediaopt, 'maxuploadsize' => $maxuploadsize, 'filtercode' => $filtercode, 'optimizeimage' => $optimizeimage]) ?>

<main class="media">


<nav class="media">
    <div class="block">
    <h2>Explorer</h2>
        <div class="scroll">
            <table id="dirlsit">
            <tr><th>folder</th><th>files</th></tr>

            <?php


            \Wcms\Modelmedia::treecount($dirlist, 'media', 0, 'media', $mediaopt->dir(), $mediaopt);

            ?>

            </table>
        </div>
        
    </div>
</nav>

<div id="filter">
    <div class="block">
        <h2>filter</h2>
        <div class="scroll">
            <form action="" method="get">
                <fieldset>
                    <legend>Type</legend>
                    <?= checkboxes('type', Wcms\Media::mediatypes(), $mediaopt->type()) ?>
                </fieldset>
                <fieldset>
                    <legend>Sort</legend>
                    <select name="sortby" id="sortby">
                        <?= options(Wcms\Modelmedia::MEDIA_SORTBY, $mediaopt->sortby()) ?>
                    </select>
                    <ul>
                        <li>
                            <input type="radio" name="order" id="asc" value="1" <?= $mediaopt->order() == 1 ? 'checked' : '' ?>><label for="asc">ascending</label>
                        </li>
                        <li>
                            <input type="radio" name="order" id="desc" value="-1" <?= $mediaopt->order() == -1 ? 'checked' : '' ?>><label for="desc">descending</label>
                        </li>
                    </ul>
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
        <span class="right">
            <a href="<?= $mediaopt->getpathaddress() ?>&display=list" <?= $workspace->mediadisplay() === Wcms\Workspace::LIST ? 'class="selected"' : '' ?> >
                <i class="fa fa-th-list"></i>
            </a>
            <a href="<?= $mediaopt->getpathaddress() ?>&display=gallery"  <?= $workspace->mediadisplay() === Wcms\Workspace::GALLERY ? 'class="selected"' : '' ?>  >
                <i class="fa fa-th-large"></i>
            </a>
        </span>
    </h2>

    <div class="scroll">


    <?php if($workspace->mediadisplay() === Wcms\Workspace::GALLERY) : ?>


        <!-- ___________________ GALLERY _________________________ -->


        <ul id="gallery">
            <?php foreach ($medialist as $media) : ?>

            <li title="<?= $media->size('hr') ?> | <?= $media->uid('name') ?> | <?= $media->permissions() ?>">
                <div class="thumbnail">
                <label for="media_<?= $media->filename() ?>">
                    <?php if($media->type() === 'image') : ?>
                        <img src="<?= $media->getabsolutepath() ?>" loading="lazy">
                    <?php elseif($media->type() === 'video' || $media->type() === 'sound') : ?>
                        <?= $media->getcode(true) ?>
                    <?php elseif($media->type() === 'font') : ?>
                        <span  style="<?= $media->getcode() ?>">Abc</span>
                    <?php else : ?>
                        <i class="fa fa-<?= $media->getsymbol() ?>"></i>
                    <?php endif ?>
                        
                </label>
                </div>
                
                <div class="meta">
                    <input type="checkbox" name="id[]" value="<?= $media->getlocalpath() ?>" form="mediaedit" id="media_<?= $media->filename() ?>">
                    <label for="media_<?= $media->filename() ?>"><?= $media->filename() ?></label>
                    <a href="<?= $media->getabsolutepath() ?>" target="_blank"><i class="fa fa-external-link"></i></a>
                    <code class="select-all"><?= $this->e($media->getcode()) ?></code>
                </div>
                    
            </li>
            
            <?php endforeach ?>
        </ul>


    <?php else : ?>



        <!-- ___________________ L I S T _________________________ -->


        <table id="medialist">
            <thead>
                <tr>
                    <th id="checkall">x</th>
                    <th>
                        <a href="<?= $mediaopt->getsortbyaddress('filename') ?>">filename</a>
                        <?= $this->insert('macro_tablesort', ['opt' => $mediaopt, 'th' => 'filename']) ?>
                    </th>
                    <th>
                        <a href="<?= $mediaopt->getsortbyaddress('extension') ?>">ext</a>
                        <?= $this->insert('macro_tablesort', ['opt' => $mediaopt, 'th' => 'extension']) ?>
                    </th>
                    <th>
                        <a href="<?= $mediaopt->getsortbyaddress('type') ?>">type</a>
                        <?= $this->insert('macro_tablesort', ['opt' => $mediaopt, 'th' => 'type']) ?>
                    </th>
                    <th>
                        <a href="<?= $mediaopt->getsortbyaddress('size') ?>">size</a>
                        <?= $this->insert('macro_tablesort', ['opt' => $mediaopt, 'th' => 'size']) ?>
                    </th>
                    <th>
                        <a href="<?= $mediaopt->getsortbyaddress('date') ?>">date</a>
                        <?= $this->insert('macro_tablesort', ['opt' => $mediaopt, 'th' => 'date']) ?>
                    </th>
                    <th>user</th>
                    <th>perms</th>
                    <th>s</th>
                    <th>code</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($medialist as $media) : ?>
                    <tr>
                    <td><input type="checkbox" name="id[]" value="<?= $media->getlocalpath() ?>" form="mediaedit" id="media_<?= $media->filename() ?>"></td>
                    <td class="filename">
                        <?php if ($user->issupereditor()) : ?>
                        <details>
                            <summary>
                                <i class="fa fa-pencil"></i>
                            </summary>
                            <form action="<?= $this->url('mediarename') ?>" method="post">
                                <input type="hidden" name="route" value="<?= $mediaopt->getpathaddress() ?>">
                                <input type="hidden" name="dir" value="<?= $media->dir() ?>">
                                <input type="hidden" name="oldfilename" value="<?= $media->filename() ?>">
                                <input type="text" name="newfilename" value="<?= $media->filename() ?>" id="newid-<?= $media->filename() ?>" maxlength="<?= Wcms\Model::MAX_ID_LENGTH ?>" minlength="1" required>
                                <input type="submit" value="rename">
                            </form>
                        </details>
                        <label for="media_<?= $media->filename() ?>"><?= $media->filename() ?></label>
                        <?php else : ?>
                            <span><?= $media->filename() ?></span>
                        <?php endif ?>
                    </td>    
                    <td><?= $media->extension() ?></td>
                    <td class="nowrap">
                        <a href="<?= $media->getabsolutepath() ?>" target="_blank">
                            <?php if($media->type() === 'image') : ?>
                                <span class="thumbnail">
                                    <i class="fa fa-<?= $media->getsymbol() ?>"></i>
                                    <img src="<?= $media->getabsolutepath() ?>" class="lightbox" loading="lazy">
                                </span>
                            <?php elseif ($media->type() === 'font' && $mediaopt->isfontdir()) : ?>
                                <span class="thumbnail">
                                    <i class="fa fa-<?= $media->getsymbol() ?>"></i>
                                    <p class="lightbox" style="<?= $media->getcode() ?>">Zut ! Je crois que le chien Sambuca préfère le whisky revigorant au doux porto.</p>
                                </span>
                            <?php else : ?>
                                <i class="fa fa-<?= $media->getsymbol() ?>"></i>
                            <?php endif ?>
                        </a>
                    </td>
                    <td class="nowrap"><?= $media->size('hr') ?></td>
                    <td class="nowrap" title="<?= $media->date('dmy') ?> <?= $media->date('ptime') ?>"><?= $media->date('hrdi') ?></td>
                    <td><?= $media->uid('name') ?></td>
                    <td><code><?= $media->permissions() ?></code></td>
                    <td>
                        <a
                            href="<?= $this->url('home', [], '?search=' . $media->getlocalpath() . '&id=1&title=1&description=1&content=1&other=1&case=1&submit=reset') ?>"
                            title="search if this media is used in your pages"
                        >
                            <i class="fa fa-search"></i>
                        </a>
                    </td>
                    <td>
                        <code class="select-all"><?= $this->e($media->getcode()) ?></code>
                    </td>
                    </tr>
                <?php endforeach ?>
            </tbody>        
        </table>


    <?php endif ?>


</div>
</section>

</main>

<?php if(!Wcms\Config::disablejavascript()) : ?>

<script type="module" src="<?= Wcms\Model::jspath() ?>media.bundle.js"></script>

<?php endif ?>



<?php $this->stop('page') ?>
