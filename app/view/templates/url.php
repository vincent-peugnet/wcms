<?php

$this->layout('backlayout', ['title' => 'URL management', 'stylesheets' => [$css . 'back.css', $css . 'url.css'], 'theme' => $theme]) ?>


<?php $this->start('page') ?>

<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'url', 'pagelist' => $pagelist]) ?>

<?php $this->insert('urlmenu', ['user' => $user]); ?>

<main class="url">
    <section>
        <h2>Urls</h2>
        <div class="scroll">
            <table>
                <thead>
                    <th>
                        x
                    </th>
                    <th>
                        URL
                    </th>
                    <th>
                        response
                    </th>
                    <th>
                        message
                    </th>
                    <th>
                        last checked
                    </th>
                    <th>
                        expire
                    </th>
                    <th>
                        edit
                    </th>
                    <th>
                        re-check
                    </th>
                </thead>

                <?php foreach($urls as $id => $url) : ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="" id="">
                        </td>
                        <td class="url" <?=  strlen($id) > 30 ? "title=\"$id\"" : '' ?>>
                            <a href="<?= $id ?>"><?= $id ?></a>
                        </td>
                        <td>
                            <span class="response" <?= $url->response > 100 ? "data-httpcode=\"$url->response\"" : '' ?>>
                                <?= $url->response ?>
                            </span>
                        </td>
                        <td>
                            <?= $url->message ?>
                        </td>
                        <td title="<?= $this->datemedium($url->timestampdate()) ?>">
                            <?= hrdi($url->timestampdate()->diff($now)) ?> ago
                        </td>
                        <td title="<?= $this->datemedium($url->expiredate()) ?>">
                            <?php if ($url->expiredate() > $now) : ?>
                                in <?= hrdi($url->expiredate()->diff($now)) ?>
                            <?php else : ?>
                                <?= hrdi($url->expiredate()->diff($now)) ?> ago
                            <?php endif ?>
                        </td>
                        <td>
                            <a href="" class="button">
                                <i class="fa fa-pencil"></i>
                            </a>
                        </td>
                        <td>
                            <a href="" class="button">
                                <i class="fa fa-refresh"></i>
                            </a>
                        </td>
                    </tr>    
                <?php endforeach ?>
            </table>
        </div>
    </section>
</main>

<?php $this->stop('page') ?>
