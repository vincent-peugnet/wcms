<?php $this->layout('layout', ['title' => '✏ '.$art->title(), 'css' => $css . 'edit.css']) ?>




<?php $this->start('page') ?>

<style>.tabs textarea{font-size: <?= Config::fontsize() ?>px}</style>

<body>
<section class="editor">

    <?php $this->insert('navart', ['user' => $user, 'art' => $art, 'artexist' => $artexist]) ?>



    <?php $this->insert('edittopbar', ['art' => $art]) ?>

    <div id="workspace">

    <?php $this->insert('editleftbar', ['art' => $art, 'tablist' => $tablist, 'artlist' => $artlist, 'showleftpanel' => $showleftpanel]) ?>
    <?php $this->insert('edittabs', ['tablist' => $tablist, 'opentab' => $art->interface()]) ?>
    <?php $this->insert('editrightbar', ['art' => $art, 'artlist' => $artlist, 'showrightpanel' => $showrightpanel]) ?>

    </div>

</form>

</section>
</body>

<?php $this->stop() ?>