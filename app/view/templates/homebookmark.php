
<section class="bookmarks">

    <div class="block">

        <h2>Bookmarks</h2>
        <strong>public</strong>

        <table>
            <tbody>
                <?php foreach ($publicbookmarks as $bookmark) { ?>
                <tr>
                        <td>
                            <a
                                href="<?= $this->url($bookmark->route(), $bookmark->params(), $bookmark->query()) ?>"
                                data-current="<?= isset($queryaddress) && $bookmark->query() === $queryaddress ? '1' : '0' ?>"
                                class="bookmark"
                                title="<?= $bookmark->description() ?>"
                            >
                            <?= $bookmark->icon() ?> <?= empty($bookmark->name()) ? $bookmark->id() : $bookmark->name() ?>
                            </a>
                        </td>
                        <td>
                            <?= $bookmark->ispublished() ? '<i class="fa fa-rss"></i>' : "" ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

            <strong>personal</strong>
        
        <table>
            <?php foreach ($personalbookmarks as $bookmark) { ?>
                <tr>
                    <td>
                        <a
                            href="<?= $this->url($bookmark->route(), $bookmark->params(), $bookmark->query()) ?>"
                            data-current="<?= isset($queryaddress) && $bookmark->query() === $queryaddress ? '1' : '0' ?>"
                            class="bookmark"
                            title="<?= $bookmark->description() ?>"
                        >
                        <?= $bookmark->icon() ?> <?= empty($bookmark->name()) ? $bookmark->id() : $bookmark->name() ?>
                        </a>
                    </td>
                    <td>
                        <?= $bookmark->ispublished() ? '<i class="fa fa-rss"></i>' : "" ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

</section>
