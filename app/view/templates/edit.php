<?php $this->layout('layout', ['title' => '✏ '.$art->title(), 'css' => $css . 'edit.css', 'favicon' => $art->favicon()]) ?>




<?php $this->start('page') ?>

<style>.tabs textarea{font-size: <?= Config::fontsize() ?>px}</style>

<body>
<main class="editor">

    <!-- <?php $this->insert('navart', ['user' => $user, 'art' => $art, 'artexist' => $artexist]) ?> -->



    <?php $this->insert('edittopbar', ['art' => $art, 'user' => $user]) ?>

    <div id="workspace">

    <?php $this->insert('editleftbar', ['art' => $art, 'tablist' => $tablist, 'artlist' => $artlist, 'showleftpanel' => $showleftpanel, 'faviconlist' => $faviconlist]) ?>
    <?php $this->insert('edittabs', ['tablist' => $tablist, 'opentab' => $art->interface(), 'templates' => $art->template()]) ?>
    <?php $this->insert('editrightbar', ['art' => $art, 'artlist' => $artlist, 'showrightpanel' => $showrightpanel, 'templates' => $art->template(), 'tagartlist' => $tagartlist, 'lasteditedartlist' => $lasteditedartlist, 'inviteuserlist' => $inviteuserlist, 'user' => $user]) ?>

    </div>

</form>

</main>
</body>

<?php $this->stop('page') ?>