<?php $this->layout('layout', ['title' => '✏ '.$art->title()]) ?>





<?php $this->start('customhead') ?>
    <script src="./rsc/js/app.js"></script>
<?php $this->stop() ?>






<?php $this->start('page') ?>

<body>

    <?php $this->insert('navart', ['user' => $user, 'art' => $art, 'artexist' => $artexist]) ?>



    <?php $this->insert('edittopbar', ['art' => $art]) ?>
    <?php $this->insert('editsidebar', ['art' => $art]) ?>
       

<style>textarea{font-size: <?= Config::fontsize() ?>px}</style>

    <?php 
    $tablist = ['section' => $art->md(), 'css' => $art->css(), 'header' => $art->header(), 'nav' => $art->nav(), 'aside' => $art->aside(), 'footer' => $art->footer(), 'html' => $art->html(), 'javascript' => $art->javascript()];
    $this->insert('edittabs', ['tablist' => $tablist, 'opentab' => $art->interface()]) 
    ?>

  


<input type="hidden" name="id" value="<?= $art->id() ?>">

</form>

</body>

<?php $this->stop() ?>