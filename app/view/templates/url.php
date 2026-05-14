<?php

$this->layout('backlayout', ['title' => 'URL management', 'stylesheets' => [$css . 'back.css', $css . 'url.css'], 'theme' => $theme]) ?>


<?php $this->start('page') ?>

<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'url', 'pagelist' => $pagelist]) ?>

<?php $this->insert('urlmenu', ['user' => $user]); ?>

<main class="url">

    <aside id="filter" class="toggle-panel-container">
        <input id="showurlfilterpanel" name="showurlfilterpanel" value="1" class="toggle-panel-toggle" type="checkbox" form="workspace-form" <?= $workspace->showurlfilterpanel() === true ? 'checked' : '' ?>>
        <label for="showurlfilterpanel" class="toggle-panel-label"><span><i class="fa fa-filter"></i></span></label>
        <div class="toggle-panel" id="filterpanel">
            <h2>Filter</h2>   
            <div class="toggle-panel-content">
                <form action="" method="get" class="flexcol">
                    <fieldset class="flexcol">
                        <legend>Sort</legend>
                        <p class="field">
                            <label for="sortby">Sort by</label>    
                            <select name="sortby" id="sortby">
                                <option value="id" <?= $sortby === 'id' ? 'selected' : '' ?>>URL</option>
                                <option value="accepted" <?= $sortby === 'accepted' ? 'selected' : '' ?>>alive</option>
                                <option value="response" <?= $sortby === 'response' ? 'selected' : '' ?>>code</option>
                                <option value="pages" <?= $sortby === 'pages' ? 'selected' : '' ?>>pages</option>
                                <option value="timestamp" <?= $sortby === 'timestamp' ? 'selected' : '' ?>>last checked</option>
                                <option value="expire" <?= $sortby === 'expire' ? 'selected' : '' ?>>expire</option>
                            </select>
                        </p>
                        <p class="field">
                            <label for="asc">ascending</label>
                            <input type="radio" name="order" id="asc" value="1" <?= $order === 1 ? 'checked' : '' ?>>
                        </p>
                        <p class="field">
                            <label for="desc">descending</label>
                            <input type="radio" name="order" id="desc" value="-1" <?= $order === -1 ? 'checked' : '' ?>>
                        </p>
                    </fieldset>
                    <fieldset class="flexcol">
                        <legend>Response</legend>
                        <p class="field">
                            <label for="response">response code</label>
                            <input type="number" name="response" id="response" min="0" max="600" value="<?= $response ?? '' ?>">
                        </p>
                    </fieldset>
                    <fieldset class="flexcol">
                        <legend>Page</legend>
                        <p class="field">
                            <label for="page">page</label>
                            <select name="page" id="page">
                                <option value="" <?= empty($page) ? 'selected' : '' ?>>--all pages--</option>
                                <?php foreach ($pages as $id) : ?>
                                    <option value="<?= $id ?>" <?= $page === $id ? 'selected' : '' ?>><?= $id ?></option>
                                <?php endforeach ?>
                            </select>
                        </p>
                    </fieldset>
                    <p class="field submit-field">
                        <input type="submit" value="filter">
                    </p>
                </form>
            </div>
        </div>
    </aside>


    <section>
        <h2>Urls (<?= $total ?>)</h2>
        <div class="scroll">
            <table>
                <thead class="sticky">
                    <th id="checkall">
                        x
                    </th>
                    <th>
                        <a href="<?= $this->url('url', [], "?sortby=id&order=$reverseorder&response=$response&page=$page") ?>">URL</a>
                        <?php if($sortby === 'id') : ?>
                            <i class="fa fa-sort-<?= $reverseorder > 0 ? 'asc' : 'desc' ?>"></i>
                        <?php endif ?>
                    </th>
                    <th>
                        link
                    </th>
                    <th>
                        <a href="<?= $this->url('url', [], "?sortby=accepted&order=$reverseorder&response=$response&page=$page") ?>">
                            <i class="fa fa-heartbeat"></i>
                        </a>
                        <?php if($sortby === 'accepted') : ?>
                            <i class="fa fa-sort-<?= $reverseorder > 0 ? 'asc' : 'desc' ?>"></i>
                        <?php endif ?>
                    </th>
                    <th>
                        <a href="<?= $this->url('url', [], "?sortby=response&order=$reverseorder&response=$response&page=$page") ?>">code</a>
                        <?php if($sortby === 'response') : ?>
                            <i class="fa fa-sort-<?= $reverseorder > 0 ? 'asc' : 'desc' ?>"></i>
                        <?php endif ?>
                    </th>
                    <th>
                        message
                    </th>
                    <th>
                        <a href="<?= $this->url('url', [], "?sortby=pages&order=$reverseorder&response=$response&page=$page") ?>">pages</a>
                        <?php if($sortby === 'pages') : ?>
                            <i class="fa fa-sort-<?= $reverseorder > 0 ? 'asc' : 'desc' ?>"></i>
                        <?php endif ?>
                    </th>
                    <th>
                        <a href="<?= $this->url('url', [], "?sortby=timestamp&order=$reverseorder&response=$response&page=$page") ?>">last checked</a>
                        <?php if($sortby === 'timestamp') : ?>
                            <i class="fa fa-sort-<?= $reverseorder > 0 ? 'asc' : 'desc' ?>"></i>
                        <?php endif ?>
                    </th>
                    <th>
                        <a href="<?= $this->url('url', [], "?sortby=expire&order=$reverseorder&response=$response&page=$page") ?>">expire</a>
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
                            <?= $url->accepted ? '<span title="OK" class="ok"><i class="fa fa-check"></i></span>' : '<span title="dead" class="dead"><i class="fa fa-times"></i></span>' ?>
                        </td>
                        <td class="response">
                            <?= $url->response ?>
                        </td>
                        <td class="message nowrap">
                            <?= $url->message ?>
                        </td>
                        <td class="pages">
                            <?php foreach ($url->pages as $page => $value) : ?>
                                <a class="button" href="<?= $this->upage('pageedit', $page) ?>"><?= $page ?></a>
                            <?php endforeach ?>
                        </td>
                        <td class="timestamp nowrap" title="<?= $this->datemedium($url->timestampdate()) ?>">
                            <?= hrdi($url->timestampdate()->diff($now)) ?> ago
                        </td>
                        <td class="expire nowrap" title="<?= $this->datemedium($url->expiredate()) ?>">
                            <?php if ($url->expiredate() > $now) : ?>
                                in <?= hrdi($url->expiredate()->diff($now)) ?>
                            <?php else : ?>
                                expired
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
