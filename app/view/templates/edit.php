<?php
    $this->layout('backlayout', ['title' => '✏ ' . $this->e($page->title()), 'theme' => $theme, 'stylesheets' => [
        Wcms\Model::jspath() . 'edit.bundle.css',
        $css . 'tagify.css',
        $css . 'edit.css',
        $css . 'tagcolors.css'
    ], 'favicon' => $page->favicon()]) 
?>

<?php $this->start('page') ?>

<style>.tabs textarea{font-size: <?= $workspace->fontsize() ?>px}</style>

<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'edit', 'pagelist' => $pagelist, 'pageid' => $page->id()]) ?>
<?php $this->insert('edittopbar', ['page' => $page, 'user' => $user, 'workspace' => $workspace, 'target' => $target]) ?>

<main class="editor">

    <?php $this->insert('editleftbar', ['page' => $page, 'pagelist' => $pagelist, 'faviconlist' => $faviconlist, 'thumbnaillist' => $thumbnaillist, 'pagelist' => $pagelist, 'editorlist' => $editorlist, 'user' => $user, 'workspace' => $workspace]) ?>
    <?php $this->insert('edittabs', ['page' => $page]) ?>
    <?php $this->insert('editrightbar', ['page' => $page, 'workspace' => $workspace, 'homebacklink' => $homebacklink, 'urls' => $urls, 'now' => $now]) ?>

</main>

<?php if(!Wcms\Config::disablejavascript()) : ?>

    <script>
        const pageversion = <?= $this->e($page->version()) ?>;
        const pageid = '<?= $this->e($page->id()) ?>';
        let pagetitle = '<?= $this->e($page->title()) ?>';
        let theme = '<?= $this->e($workspace->highlighttheme()) ?>';
    </script>
    <script src="<?= Wcms\Model::jspath() ?>taglist.js"></script>
    <script type="module" src="<?= Wcms\Model::jspath() ?>edit.bundle.js" async></script>

<?php endif ?>

<?php $this->stop('page') ?>
