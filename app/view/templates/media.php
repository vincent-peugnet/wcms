<?php $this->layout('layout', ['title' => 'media', 'stylesheets' => [$css . 'back.css', $css . 'media.css', $cssfont]]) ?>

<?php $this->start('page') ?>

<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'media', 'pagelist' => $pagelist]) ?>

<?php $this->insert('mediamenu', ['user' => $user, 'pathlist' => $pathlist, 'mediaopt' => $mediaopt, 'maxuploadsize' => $maxuploadsize, 'filtercode' => $filtercode, 'optimizeimage' => $optimizeimage]) ?>

<main class="media">

    <aside id="media" class="toggle-panel-container">
        <input id="showemediapanel" name="showemediapanel" value="1" class="toggle-panel-toggle" type="checkbox" form="workspace-form" checked>
        <label for="showemediapanel" class="toggle-panel-label"><span><i class="fa fa-folder"></i></span></label>
        <div class="toggle-panel" id="mediapanel">
            <h2>Explorer</h2>
            <div class="toggle-panel-content">
                <table id="dirlist">
                    <?php \Wcms\Modelmedia::treecount($dirlist, 'media', 0, 'media', $mediaopt->dir(), $mediaopt); ?>
                </table>
            </div>        
        </div>
    </aside>

    <aside id="filter" class="toggle-panel-container">

        <input id="showeoptionspanel" name="showeoptionspanel" value="1" class="toggle-panel-toggle" type="checkbox" form="workspace-form" >
        <label for="showeoptionspanel" class="toggle-panel-label"><span><i class="fa fa-filter"></i></span></label>
        
        <div class="toggle-panel" id="optionspanel">
            <h2>Filters</h2>
            <div class="toggle-panel-content">
                <form action="" method="get" class="flexcol">
                    <fieldset class="flexcol">
                        <legend>Type</legend>
                        <?php 
                            $optionlist = Wcms\Media::mediatypes(); 
                            $checkedlist = $mediaopt->type();
                            foreach ($optionlist as $option) :
                        ?>
                        <p class="field">
                            <label for="<?= $option ?>"><?= $option ?></label>
                            <input type="checkbox" name="type[]" id="<?= $option ?>" value="<?= $option ?>" <?= in_array($option, $checkedlist) ? "checked" : "" ?>>
                        </p>
                        <?php endforeach ?>
                    </fieldset>
                    <fieldset class="flexcol">
                        <legend>Sort</legend>
                        <p class="field">
                            <label for="sortby">Sort by</label>    
                            <select name="sortby" id="sortby">
                                <?= options(Wcms\Modelmedia::MEDIA_SORTBY, $mediaopt->sortby()) ?>
                            </select>
                        </p>
                        <p class="field">
                            <label for="asc">ascending</label>
                            <input type="radio" name="order" id="asc" value="1" <?= $mediaopt->order() == 1 ? 'checked' : '' ?>>
                        </p>
                        <p class="field">
                            <label for="desc">descending</label>
                            <input type="radio" name="order" id="desc" value="-1" <?= $mediaopt->order() == -1 ? 'checked' : '' ?>>
                        </p>
                    </fieldset>
                    <p class="field submit-field">
                        <input type="hidden" name="path" value="<?= $mediaopt->path() ?>">
                        <input type="submit" value="filter">
                    </p>
                </form>
            </div>
        </div>
    </aside>

    <section>

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
