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
                        <a href="<?= $this->url('url', [], "?sortby=id&order=$reverseorder") ?>">URL</a>
                        <?php if($sortby === 'id') : ?>
                            <i class="fa fa-sort-<?= $reverseorder > 0 ? 'asc' : 'desc' ?>"></i>
                        <?php endif ?>
                    </th>
                    <th>
                        link
                    </th>
                    <th>
                        <a href="<?= $this->url('url', [], "?sortby=accepted&order=$reverseorder") ?>">
                            <i class="fa fa-heart"></i>
                        </a>
                        <?php if($sortby === 'accepted') : ?>
                            <i class="fa fa-sort-<?= $reverseorder > 0 ? 'asc' : 'desc' ?>"></i>
                        <?php endif ?>
                    </th>
                    <th>
                        <a href="<?= $this->url('url', [], "?sortby=response&order=$reverseorder") ?>">code</a>
                        <?php if($sortby === 'response') : ?>
                            <i class="fa fa-sort-<?= $reverseorder > 0 ? 'asc' : 'desc' ?>"></i>
                        <?php endif ?>
                    </th>
                    <th>
                        message
                    </th>
                    <th>
                        <a href="<?= $this->url('url', [], "?sortby=timestamp&order=$reverseorder") ?>">last checked</a>
                        <?php if($sortby === 'timestamp') : ?>
                            <i class="fa fa-sort-<?= $reverseorder > 0 ? 'asc' : 'desc' ?>"></i>
                        <?php endif ?>
                    </th>
                    <th>
                        <a href="<?= $this->url('url', [], "?sortby=expire&order=$reverseorder") ?>">expire</a>
                        <?php if($sortby === 'expire') : ?>
                            <i class="fa fa-sort-<?= $reverseorder > 0 ? 'asc' : 'desc' ?>"></i>
                        <?php endif ?>
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
                            <a href="<?= $url->id ?>" class="button"><i class="fa fa-link"></i></a>
                        </td>
                        <td>
                            <?= $url->accepted ? '<span title="OK">✅</span>' : '<span title="dead">💀</span>' ?>
                        </td>
                        <td class="response">
                            <?= $url->response ?>
                        </td>
                        <td class="message">
                            <?= $url->message ?>
                        </td>
                        <td class="timestamp" title="<?= $this->datemedium($url->timestampdate()) ?>">
                            <?= hrdi($url->timestampdate()->diff($now)) ?> ago
                        </td>
                        <td class="expire" title="<?= $this->datemedium($url->expiredate()) ?>">
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
