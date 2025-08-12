<aside id="rightbar" class="toggle-panel-container">
    <input id="showeditorrightpanel" name="showeditorrightpanel" value="1" class="toggle-panel-toggle" type="checkbox" <?= $workspace->showeditorrightpanel() === true ? 'checked' : '' ?> form="workspace-form" >
    <label for="showeditorrightpanel" class="toggle-panel-label"><span><i class="fa fa-info"></i></span></label>

    <div class="toggle-panel" id="rightbarpanel">

        <h2>Infos</h2>
    
        <div class="toggle-panel-content flexcol">
            <h3>Stats</h3>

            <table>
                <tbody>
                    <tr>
                        <td>edition:</td>
                        <td><?= $page->editcount() ?></td>
                    </tr>
                    <tr>
                        <td>display:</td>
                        <td><?= $page->displaycount() ?></td>
                    </tr>
                    <tr>
                        <td>visit:</td>
                        <td><?= $page->visitcount() ?></td>
                    </tr>
                </tbody>
            </table>

            <h3>internal links: <?= count($page->linkto()) ?></h3>

            <ul class="internallinks">
                <?php foreach ($page->linkto() as $link) : ?>
                    <li>
                        <a href="<?= $this->upage('pageread', $link) ?>" class="read" target="_blank" <?= strlen($link) > 30 ? "title=\"$link\"" : '' ?>>
                            <?= $link ?>
                        </a>
                        <a href="<?= $this->upage('pageedit', $link) ?>" class="button">
                            <i class="fa fa-pencil"></i>
                        </a>
                    </li>
                <?php endforeach ?>
            </ul>

            <h3>back links</h3>

            <a href="<?= $this->url('home', [], $homebacklink) ?>" class="button">
                <i class="fa fa-list-ul"></i>
                list pages that link here
            </a>

            <h3>external links: <?= count($page->externallinks()) ?></h3>

            <ul class="externallinks">
                <?php foreach ($page->externallinks() as $url => $status) : ?>
                    <?php if(Wcms\Config::urlchecker() && key_exists($url, $urls)) : ?>
                        <li title="<?= $url ?>&#013;<?= $urls[$url]['response'] < 100 ? '❌' : '💡 ' . $urls[$url]['response']  ?> <?= $urls[$url]['message'] ?>&#013;⏱️ checked <?= hrdi($urls[$url]['timestamp']->diff($now)) ?> ago">
                    <?php else : ?>
                        <li title="<?= $url ?>">
                    <?php endif ?>
                        <a href="<?= $url ?>" target="_blank">
                            <?= ltrim(substr($url, 6), "\/") ?>
                        </a>
                        <span>
                            <?= is_bool($status) ? ($status ? '✅' : '💀') : '' ?>
                        </span>
                    </li>
                <?php endforeach ?>
            </ul>

            <h3>Help</h3>
            <div id="help">
                <?php $this->insert('edithelp') ?>
            </div>           
        </div>

    </div>

</aside>
