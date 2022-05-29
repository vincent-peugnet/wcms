
<section class="bookmarks">

    <div class="block">

        <h2>Bookmarks</h2>
        <strong>public</strong>

        <ul>
            <?php foreach ($publicbookmarks as $bookmark) { ?>
                <li>
                    <a
                        href="<?= $this->url($bookmark->route(), $bookmark->params(), $bookmark->query()) ?>"
                        data-current="<?= isset($queryaddress) && $bookmark->query() === $queryaddress ? '1' : '0' ?>"
                        class="bookmark"
                        title="<?= $bookmark->description() ?>"
                    >
                    <?= $bookmark->icon() ?> <?= empty($bookmark->name()) ? $bookmark->id() : $bookmark->name() ?>
                    </a>
                </li>
            <?php } ?>
        </ul>

        <strong>personal</strong>

        <ul>
            <?php foreach ($personalbookmarks as $bookmark) { ?>
                <li>
                    <a
                        href="<?= $this->url($bookmark->route(), $bookmark->params(), $bookmark->query()) ?>"
                        data-current="<?= isset($queryaddress) && $bookmark->query() === $queryaddress ? '1' : '0' ?>"
                        class="bookmark"
                        title="<?= $bookmark->description() ?>"
                    >
                    <?= $bookmark->icon() ?> <?= empty($bookmark->name()) ? $bookmark->id() : $bookmark->name() ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>

</section>
