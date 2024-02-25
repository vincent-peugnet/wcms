<?php

$this->layout('layout', ['title' => '✏ '.$page->title(), 'stylesheets' => [
    Wcms\Model::jspath() . 'edit.bundle.css',
    $css . 'edit.css',
    $css . 'tagcolors.css'
], 'favicon' => $page->favicon()]) ?>




<?php $this->start('page') ?>

<style>.tabs textarea{font-size: <?= $workspace->fontsize() ?>px}</style>

<div class="editor">

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'edit', 'pagelist' => $pagelist, 'pageid' => $page->id()]) ?>


    <?php $this->insert('edittopbar', ['page' => $page, 'user' => $user, 'workspace' => $workspace, 'target' => $target]) ?>

    <div id="workspace">

    <?php $this->insert('editleftbar', ['page' => $page, 'pagelist' => $pagelist, 'faviconlist' => $faviconlist, 'thumbnaillist' => $thumbnaillist, 'workspace' => $workspace]) ?>
    <?php $this->insert('edittabs', ['tablist' => $page->tabs(), 'opentab' => $page->interface()]) ?>
    <?php $this->insert('editrightbar', ['page' => $page, 'pagelist' => $pagelist, 'editorlist' => $editorlist, 'user' => $user, 'workspace' => $workspace]) ?>

    </div>

</div>

<?php if(!Wcms\Config::disablejavascript()) { ?>

<script>
    const pageversion = <?= $this->e($page->version()) ?>;
    const pageid = '<?= $this->e($page->id()) ?>';
    let pagetitle = '<?= $this->e($page->title()) ?>';
    let theme = '<?= $this->e($workspace->highlighttheme()) ?>';
    const taglist = <?= json_encode($taglist) ?>;
</script>
<script type="module" src="<?= Wcms\Model::jspath() ?>edit.bundle.js" async></script>

<?php } ?>

<?php $this->stop('page') ?>
