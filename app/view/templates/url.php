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
                    <th id="checkall">
                        x
                    </th>
                    <th>
                        URL
                    </th>
                    <th>
                        link
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
                </thead>

                <?php foreach($urls as $id => $url) : ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="id[]" id="url_<?= $url->id ?>" value="<?= $url->id ?>" form="urledit">
                        </td>
                        <td class="url" <?=  strlen($url->id) > 30 ? "title=\"$url->id\"" : '' ?>>
                            <label for="url_<?= $url->id ?>"><?= $url->id ?></label>
                        </td>
                        <td>
                            <a href="<?= $url->id ?>" class="button"><i class="fa fa-link"></i></a></td>
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
                    </tr>    
                <?php endforeach ?>
            </table>
        </div>
    </section>
</main>

<?php if(!Wcms\Config::disablejavascript()) : ?>
    <script type="module" src="<?= Wcms\Model::jspath() ?>url.bundle.js"></script>
<?php endif ?>

<?php $this->stop('page') ?>
