
<section class="bookmarks">

    <div class="block">

        <h2>Bookmarks</h2>

        <ul>
            <?php foreach ($bookmarks as $bookmark) { ?>
                <li>
                    <a
                        href="<?= $this->url($bookmark->route(), $bookmark->params(), $bookmark->query()) ?>"
                        data-current="<?= isset($queryaddress) && $bookmark->query() === $queryaddress ? '1' : '0' ?>"
                        class="bookmark"
                    >
                    <?= $bookmark->icon() ?> <?= $bookmark->id() ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>

</section>