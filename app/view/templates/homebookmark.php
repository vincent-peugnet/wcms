<aside id="bookmarks" class="toggle-panel-container">
    <input id="showhomebookmarkspanel" name="showhomebookmarkspanel" value="1" class="toggle-panel-toggle" type="checkbox" form="workspace-form"  <?= !$workspace->collapsemenu() && $workspace->showhomebookmarkspanel() === true ? 'checked' : '' ?>>
    <label for="showhomebookmarkspanel" class="toggle-panel-label"><span><i class="fa fa-bookmark"></i></span></label>

    <div class="toggle-panel">
        <h2>Bookmarks</h2>
        <div class="toggle-panel-content">
            <div class="flexcol">
                <h3>Public</h3>
                <?php foreach ($publicbookmarks as $bookmark) : ?>
                    <p
                        class="bookmark"
                        data-current="<?= isset($queryaddress) && $bookmark->query() === $queryaddress ? '1' : '0' ?>"
                        title="<?= strlen($bookmark->name()) > 15 ? $this->e($bookmark->name()) . '&#013;' : '' ?><?= $this->e($bookmark->description()) ?>"

                    >
                        <a
                            href="<?= $this->url("home", [], $bookmark->query()) ?>&display=<?= $display ?>"                            
                        >
                            <span class="icon"><?= $bookmark->icon() ?></span>
                            <span class="name"><?= empty($bookmark->name()) ? $bookmark->id() : $this->e($bookmark->name()) ?></span>
                        </a>                            
                        <?php if($bookmark->ispublished()) : ?>
                            <a href="<?= Wcms\Servicerss::atomfile($bookmark->id()) ?>" target="_blank" title="This bookmark is used to create a news feed&#013;click to show Atom XML file">
                                <i class="fa fa-rss"></i>
                            </a>
                        <?php endif ?>
                    </p>
                <?php endforeach ?>
        
                <h3>Personal</h3>
                <?php foreach ($personalbookmarks as $bookmark) : ?>
                    <p
                        class="bookmark"
                        data-current="<?= isset($queryaddress) && $bookmark->query() === $queryaddress ? '1' : '0' ?>"
                        title="<?= strlen($bookmark->name()) > 15 ? $this->e($bookmark->name()) . '&#013;' : '' ?><?= $this->e($bookmark->description()) ?>"
                    >
                        <a
                            href="<?= $this->url("home", [], $bookmark->query()) ?>&display=<?= $display ?>"
                        >
                            <span class="icon"><?= $bookmark->icon() ?></span>
                            <span class="name"><?= empty($bookmark->name()) ? $bookmark->id() : $this->e($bookmark->name()) ?></span>
                        </a>
                    </p>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</aside>
