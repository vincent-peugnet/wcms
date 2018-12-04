<?php $this->layout('layout', ['title' => 'font', 'css' => $css . 'home.css']) ?>


<?php $this->start('page') ?>

<body>

    <?php $this->insert('backtopbar', ['user' => $user]) ?>


<section class="font">


<a href="<?= $this->url('fontrender') ?>">⚡ Render</a>
<a href="<?= $fontfile ?>" target="_blank">👓 View font CSS file</a>


<form action="<?= $this->url('fontadd') ?>" method="post" enctype="multipart/form-data">
<label for="font">Font file <i>(<?= $fonttypes ?>)</i></label>
<input type="file" name="font" id="font" accept="<?= $fonttypes ?>">
<label for="fontname">Rename font <i>(optionnal)</i></label>
<input type="text" name="fontname" id="fontname">
<input type="submit" value="upload font(s)">
</form>


<table id="fontlist">
<tr>
<th>font</th>
<th>type</th>
<th>size</th>
</tr>
<?php

foreach ($fontlist as $font ) {
    ?>
    <tr>
    <td><?= $font['id'] ?></td>
    <td><?= $font['ext'] ?></td>
    <td><?= readablesize($font['size']) ?></td>
    </tr>
    <?php
}

?>
</table>

</section>
</body>

<?php $this->stop('page') ?>