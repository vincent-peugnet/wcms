<aside id="bookmarks" class="toggle-panel-container">
    <input id="showebookmarkspanel" name="showebookmarkspanel" value="1" class="toggle-panel-toggle" type="checkbox" checked form="workspace-form" >
    <label for="showebookmarkspanel" class="toggle-panel-label"><span><i class="fa fa-bookmark"></i></span></label>

    <div class="toggle-panel" id="optionspanel">
        <h2>Bookmarks</h2>
        <div class="toggle-panel-content">
            <h3>public</h3>

            <table>
                <tbody>
                    <?php foreach ($publicbookmarks as $bookmark) : ?>
                        <tr>
                            <td>
                                <a
                                    href="<?= $this->url("home", [], $bookmark->query()) ?>&display=<?= $display ?>"
                                    data-current="<?= isset($queryaddress) && $bookmark->query() === $queryaddress ? '1' : '0' ?>"
                                    class="bookmark"
                                    title="<?= $bookmark->description() ?>"
                                >
                                    <span class="icon">
                                        <?= $bookmark->icon() ?>
                                    </span>
                                    <span class="name">
                                        <?= empty($bookmark->name()) ? $bookmark->id() : $bookmark->name() ?>
                                    </span>
                                </a>
                            </td>
                            <td class="rss">
                                <?php if($bookmark->ispublished()) : ?>
                                    <a href="<?= Wcms\Servicerss::atomfile($bookmark->id()) ?>" target="_blank" title="show Atom XML file">
                                        <i class="fa fa-rss"></i>
                                    </a>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>

                <h3>personal</h3>
            
            <table>
                <?php foreach ($personalbookmarks as $bookmark) : ?>
                    <tr>
                        <td>
                            <a
                                href="<?= $this->url("home", [], $bookmark->query()) ?>&display=<?= $display ?>"
                                data-current="<?= isset($queryaddress) && $bookmark->query() === $queryaddress ? '1' : '0' ?>"
                                class="bookmark"
                                title="<?= $bookmark->description() ?>"
                            >
                                <?= $bookmark->icon() ?>
                                <span>
                                    <?= empty($bookmark->name()) ? $bookmark->id() : $bookmark->name() ?>
                                </span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </table>
        </div>
    </div>
</aside>
