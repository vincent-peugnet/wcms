<?php $this->layout('layout', ['title' => 'home', 'css' => $css . 'home.css', 'favicon' => '']) ?>




<?php $this->start('page') ?>


<body>

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'home']) ?>

    <?php if ($user->iseditor()) { ?>



    <?php $this->insert('homemenu', ['user' => $user, 'opt' => $opt]) ?>


    <main class="home">


        <?php $this->insert('homeopt', ['opt' => $opt, 'user' => $user]) ?>

        <section class="pages">

            <div class="block">

                <h2>Pages (<?= count($table2) ?>)</h2>


                <details id="list" class="hidephone" <?= isset($optlist) ? 'open' : '' ?>>
                    <summary>Generate list</summary>
                    <i>Generate code to display a list of pages</i>
                    <form action="<?= $this->url('homequery') ?>" method="post">
                        <input type="hidden" name="query" value="1">

                        <input type="hidden" name="description" value="0">
                        <input type="checkbox" name="description" id="list_description" value="1" <?= isset($optlist) && $optlist->description() ? 'checked' : '' ?>>
                        <label for="list_description">Show description</label>
                        </br>
                        <input type="hidden" name="thumbnail" value="0">
                        <input type="checkbox" name="thumbnail" id="list_thumbnail" value="1" <?= isset($optlist) && $optlist->thumbnail() ? 'checked' : '' ?>>
                        <label for="list_thumbnail">Show thumbnail</label>
                        </br>
                        <input type="hidden" name="date" value="0">
                        <input type="checkbox" name="date" id="list_date" value="1" <?= isset($optlist) && $optlist->date() ? 'checked' : '' ?>>
                        <label for="list_date">Show date</label>
                        </br>
                        <input type="hidden" name="author" value="0">
                        <input type="checkbox" name="author" id="list_author" value="1" <?= isset($optlist) && $optlist->author() ? 'checked' : '' ?>>
                        <label for="list_author">Show author(s)</label>
                        </br>
                        <select name="style" id="list_style">
                            <option value="0">list</option>
                            <option value="1" <?= isset($optlist) && $optlist->style() == 1 ? 'selected' : '' ?>>div</option>
                        </select>
                        <input type="submit" value="generate">
                    </form>

                    <?php
                        if (isset($optlist)) {
                            echo '<code>' . $optlist->getcode() . '</code>';
                        }

                        ?>
                </details>



                <form action="/massedit" method="post">

                </form>


                <div class="scroll">

                    <table id="home2table">
                        <thead>
                            <tr>
                                <th>x</th>
                                <th><a href="<?= $opt->getadress('id') ?>">id</a></th>
                                <th>edit</th>
                                <th>see</th>
                                <th class="delete" title="delete page">del</th>
                                <?php if ($user->issupereditor()) { ?>
                                <th class="download" title="download page as json">dl</th>
                                <?php }
                                    if ($columns['tag']) { ?>
                                <th class="tag"><a href="<?= $opt->getadress('tag') ?>">tag</a></th>
                                <?php }
                                    if ($columns['title']) { ?>
                                <th class="title"><a href="<?= $opt->getadress('title') ?>">title</a></th>
                                <?php }
                                    if ($columns['description']) { ?>
                                <th class="summary">summary</th>
                                <?php }
                                    if ($columns['linkto']) { ?>
                                <th class="linkto"><a href="<?= $opt->getadress('linkto') ?>">to</a></th>
                                <?php }
                                    if ($columns['linkfrom']) { ?>
                                <th class="linkfrom"><a href="<?= $opt->getadress('linkfrom') ?>">from</a></th>
                                <?php }
                                    if ($columns['datemodif']) { ?>
                                <th class="datemodif"><a href="<?= $opt->getadress('datemodif') ?>">last modification</a></th>
                                <?php }
                                    if ($columns['datecreation']) { ?>
                                <th class="datecreation"><a href="<?= $opt->getadress('datecreation') ?>">date of creation</a></th>
                                <?php }
                                    if ($columns['date']) { ?>
                                <th class="date"><a href="<?= $opt->getadress('date') ?>">date</a></th>
                                <?php }
                                    if ($columns['secure']) { ?>
                                <th class="secure"><a href="<?= $opt->getadress('secure') ?>">privacy</a></th>
                                <?php }
                                    if ($columns['visitcount']) { ?>
                                <th class="visitcount"><a href="<?= $opt->getadress('visitcount') ?>">visit</a></th>
                                <?php }
                                    if ($columns['editcount']) { ?>
                                <th class="editcount"><a href="<?= $opt->getadress('editcount') ?>">edit</a></th>
                                <?php }
                                    if ($columns['affcount']) { ?>
                                <th class="affcount"><a href="<?= $opt->getadress('affcount') ?>">aff</a></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($table2 as $item) { ?>
                            <tr>
                                <td><input type="checkbox" name="id[]" value="<?= $item->id() ?>" id="id_<?= $item->id() ?>"></td>
                                <td><label title="<?= $item->title() ?>" for="id_<?= $item->id() ?>"><?= $item->id() ?></label></td>
                                <td><a href="<?= $this->uart('artedit', $item->id()) ?>"><img src="<?= Model::iconpath() ?>edit.png" class="icon"></a></td>
                                <td><a href="<?= $this->uart('artread/', $item->id()) ?>" target="_blank"><img src="<?= Model::iconpath() ?>read.png" class="icon"></a></td>
                                <td class="delete"><a href="<?= $this->uart('artdelete', $item->id()) ?>">✖</a></td>
                                <?php if ($user->issupereditor()) { ?>
                                <td><a href="<?= $this->uart('artdownload', $item->id()) ?>" download><img src="<?= Model::iconpath() ?>download.png" class="icon"></a></td>
                                <?php }
                                        if ($columns['tag']) { ?>
                                <td class="tag"><a title="<?= $item->tag('string') ?>"><?= $item->tag('sort') ?></a></td>
                                <?php }
                                        if ($columns['title']) { ?>
                                <td class="title" title="<?= $item->title() ?>"><?= $item->title() ?></td>
                                <?php }
                                        if ($columns['description']) { ?>
                                <td class="summary" title="<?= $item->description() ?>"><?= $item->description('short') ?></td>
                                <?php }
                                        if ($columns['linkto']) { ?>
                                <td class="linkto"><a title="<?= $item->linkto('string') ?>"><?= $item->linkto('sort') ?></a></td>
                                <?php }
                                        if ($columns['linkfrom']) { ?>
                                <td class="linkfrom"><a title="<?= $item->linkfrom('string') ?>"><?= $item->linkfrom('sort') ?></a></td>
                                <?php }
                                        if ($columns['datemodif']) { ?>
                                <td class="datemodif"><?= $item->datemodif('hrdi') ?></td>
                                <?php }
                                        if ($columns['datecreation']) { ?>
                                <td class="datecreation"><?= $item->datecreation('hrdi') ?></td>
                                <?php }
                                        if ($columns['date']) { ?>
                                <td class="date"><?= $item->date('dmy') ?></td>
                                <?php }
                                        if ($columns['secure']) { ?>
                                <td class="secure"><?= $item->secure('string') ?></td>
                                <?php }
                                        if ($columns['visitcount']) { ?>
                                <td class="visitcount"><?= $item->visitcount() ?></td>
                                <?php }
                                        if ($columns['editcount']) { ?>
                                <td class="editcount"><?= $item->editcount() ?></td>
                                <?php }
                                        if ($columns['affcount']) { ?>
                                <td class="affcount"><?= $item->affcount() ?></td>
                                <?php } ?>
                            </tr>

                            <?php  } ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </section>


        <?php if($user->display()['bookmark'] && (!empty(Config::bookmark()) || !empty($user->bookmark()))) { ?>

        <section class="hidephone" id="bookmark">
            <div class="block">
                <h2>Bookmarks</h2>
                <div class="scroll">
                    <strong>Public</strong>
                    <ul>
                        <?php foreach (Config::bookmark() as $id => $query) { ?>
                            <li>
                                <a href="<?= $query ?>"><?= $id ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                    <strong><?= $user->id() ?></strong>
                    <ul>
                        <?php foreach ($user->bookmark() as $id => $query) { ?>
                            <li>
                                <a href="<?= $query ?>"><?= $id ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </section>
            </div>
        </nav>

        <?php } ?>

    </main>

    <?php $this->insert('footer', ['footer' => $footer]) ?>

    <?php } ?>


</body>



<?php $this->stop() ?>