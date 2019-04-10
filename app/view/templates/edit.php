<?php $this->layout('layout', ['title' => '✏ '.$art->title(), 'css' => $css . 'edit.css', 'favicon' => $art->favicon()]) ?>




<?php $this->start('page') ?>

<style>.tabs textarea{font-size: <?= Config::fontsize() ?>px}</style>

<body>
<main class="editor">

    <?php $this->insert('edittopbar', ['art' => $art, 'user' => $user]) ?>

    <div id="workspace">

    <?php $this->insert('editleftbar', ['art' => $art, 'tablist' => $tablist, 'artlist' => $artlist, 'showleftpanel' => $showleftpanel, 'faviconlist' => $faviconlist]) ?>
    <?php $this->insert('edittabs', ['tablist' => $tablist, 'opentab' => $art->interface(), 'templates' => $art->template()]) ?>
    <?php $this->insert('editrightbar', ['art' => $art, 'artlist' => $artlist, 'showrightpanel' => $showrightpanel, 'templates' => $art->template(), 'tagartlist' => $tagartlist, 'lasteditedartlist' => $lasteditedartlist, 'editorlist' => $editorlist, 'user' => $user]) ?>

    </div>

</form>

</main>

<script>
    const artid = '<?= $art->id() ?>';
</script>
<script src="<?= Model::jspath() ?>edit.js"></script>

<?php if($codemirror) { ?>
<script src="<?= Model::jspath() ?>/codemirror/lib/codemirror.js"></script>
<link rel="stylesheet" href="<?= Model::jspath() ?>/codemirror/lib/codemirror.css">
<script src="<?= Model::jspath() ?>/codemirror/mode/javascript/javascript.js"></script>
<script src="<?= Model::jspath() ?>/codemirror/mode/css/css.js"></script>
<script src="<?= Model::jspath() ?>/codemirror/mode/markdown/markdown.js"></script>
<script src="<?= Model::jspath() ?>/codemirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="<?= Model::jspath() ?>/codemirror.js"></script>

<?php } ?>

</body>

<?php $this->stop('page') ?>