<?php $this->layout('layout', ['title' => '✏ '.$page->title(), 'stylesheets' => [$css . 'edit.css'], 'favicon' => $page->favicon()]) ?>




<?php $this->start('page') ?>

<style>.tabs textarea{font-size: <?= Wcms\Config::fontsize() ?>px}</style>

<main class="editor">

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'edit', 'pagelist' => $pagelist, 'pageid' => $page->id()]) ?>


    <?php $this->insert('edittopbar', ['page' => $page, 'user' => $user]) ?>

    <div id="workspace">

    <?php $this->insert('editleftbar', ['page' => $page, 'tablist' => $tablist, 'pagelist' => $pagelist, 'showleftpanel' => $showleftpanel, 'faviconlist' => $faviconlist, 'thumbnaillist' => $thumbnaillist]) ?>
    <?php $this->insert('edittabs', ['tablist' => $tablist, 'opentab' => $page->interface(), 'templates' => $page->template()]) ?>
    <?php $this->insert('editrightbar', ['page' => $page, 'pagelist' => $pagelist, 'showrightpanel' => $showrightpanel, 'templates' => $page->template(), 'tagpagelist' => $tagpagelist, 'lasteditedpagelist' => $lasteditedpagelist, 'editorlist' => $editorlist, 'user' => $user]) ?>

    </div>

</form>

</main>

<script>
    const pageid = '<?= $page->id() ?>';
    let pagetitle = '<?= $page->title() ?>';
</script>
<script src="<?= Wcms\Model::jspath() ?>edit.bundle.js"></script>

<?php $this->stop('page') ?>