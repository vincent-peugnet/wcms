<?php $this->layout('alertlayout') ?>

<?php $this->start('alert') ?>

<p>
    🛑 Command <code>/<?= $this->e($command) ?></code> not found
</p>

<p>👁️ <a href="<?= $this->upage('pageread', $id) ?>">read page</a> <code><?= $this->e($id) ?></code></p>

<?php if (!$user->isvisitor()) { ?>
    <p>
        💡 You may want to try:
        <ul>
            <li>
                <a href="<?= $this->upage('pageadd', $id) ?>"><code>/add</code></a> to create a new page
            </li>
            <li>
                <a href="<?= $this->upage('pageedit', $id) ?>"><code>/edit</code></a> to edit the page
            </li>
            <li>
                <a href="<?= $this->upage('pagerender', $id) ?>"><code>/render</code></a> to render the page
            </li>
            <li>
                <a href="<?= $this->upage('pagedelete', $id) ?>"><code>/delete</code></a> to delete the page
            </li>
            <li>
                <a href="<?= $this->upage('pagedownload', $id) ?>"><code>/download</code></a> to get JSON file of the page
            </li>
        </ul>
    </p>
<?php } ?>

<?php $this->stop() ?>

