<aside id="bookmarks" class="toggle-panel-container">
    <input id="showebookmarkspanel" name="showebookmarkspanel" value="1" class="toggle-panel-toggle" type="checkbox" form="workspace-form"  <?= $workspace->showebookmarkspanel() === true ? 'checked' : '' ?>>
    <label for="showebookmarkspanel" class="toggle-panel-label"><span><i class="fa fa-bookmark"></i></span></label>

    <div class="toggle-panel" id="optionspanel">
        <h2>Bookmarks</h2>
        <div class="toggle-panel-content">
            <div class="flexcol">
                <h3>Public</h3>
                <?php foreach ($publicbookmarks as $bookmark) : ?>
                    <p class="bookmark">
                        <a
                            href="<?= $this->url("home", [], $bookmark->query()) ?>&display=<?= $display ?>"
                            data-current="<?= isset($queryaddress) && $bookmark->query() === $queryaddress ? '1' : '0' ?>"
                            class="bookmark"
                            title="<?= $bookmark->description() ?>"
                        >
                            <span class="icon"><?= $bookmark->icon() ?></span>
                            <span class="name"><?= empty($bookmark->name()) ? $bookmark->id() : $bookmark->name() ?></span>
                        </a>                            
                        <?php if($bookmark->ispublished()) : ?>
                            <a href="<?= Wcms\Servicerss::atomfile($bookmark->id()) ?>" target="_blank" title="show Atom XML file">
                                <i class="fa fa-rss"></i>
                            </a>
                    </p>
                    <?php endif ?>
                <?php endforeach ?>
        
                <h3>Personal</h3>
                <?php foreach ($personalbookmarks as $bookmark) : ?>
                    <p class="bookmark">
                        <a
                            href="<?= $this->url("home", [], $bookmark->query()) ?>&display=<?= $display ?>"
                            data-current="<?= isset($queryaddress) && $bookmark->query() === $queryaddress ? '1' : '0' ?>"
                            class="bookmark"
                            title="<?= $bookmark->description() ?>"
                        >
                            <span class="icon"><?= $bookmark->icon() ?></span>
                            <span class="name"><?= empty($bookmark->name()) ? $bookmark->id() : $bookmark->name() ?></span>
                        </a>
                    </p>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</aside>
