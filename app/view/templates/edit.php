<?php $this->layout('layout', ['title' => '✏ '.$page->title(), 'css' => $css . 'edit.css', 'favicon' => $page->favicon()]) ?>




<?php $this->start('page') ?>

<style>.tabs textarea{font-size: <?= Config::fontsize() ?>px}</style>

<body>
<main class="editor">

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'edit', 'pagelist' => $pagelist, 'pageid' => $page->id()]) ?>


    <?php $this->insert('edittopbar', ['page' => $page, 'user' => $user]) ?>

    <div id="workspace">

    <?php $this->insert('editleftbar', ['page' => $page, 'tablist' => $tablist, 'pagelist' => $pagelist, 'showleftpanel' => $showleftpanel, 'faviconlist' => $faviconlist]) ?>
    <?php $this->insert('edittabs', ['tablist' => $tablist, 'opentab' => $page->interface(), 'templates' => $page->template()]) ?>
    <?php $this->insert('editrightbar', ['page' => $page, 'pagelist' => $pagelist, 'showrightpanel' => $showrightpanel, 'templates' => $page->template(), 'tagpagelist' => $tagpagelist, 'lasteditedpagelist' => $lasteditedpagelist, 'editorlist' => $editorlist, 'user' => $user]) ?>

    </div>

</form>

</main>

<script>
    const pageid = '<?= $page->id() ?>';
</script>
<script src="<?= Model::jspath() ?>edit.js"></script>
</body>

<?php $this->stop('page') ?>