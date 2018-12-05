<?php $this->layout('layout', ['title' => 'media', 'css' => $css . 'home.css']) ?>


<?php $this->start('page') ?>

<body>

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'media']) ?>


<section class="media">

<h1>Media</h1>

<table id="medialist">
<tr><th>id</th><th>extension</th><th>path</th><th>type</th><th>size</th><th>width</th><th>height</th><th>lengh</th></tr>

<?php
foreach ($medialist as $media) {
    ?>
    <tr>
    <td><?= $media->id() ?></td>
    <td><?= $media->extension() ?></td>
    <td><?= $media->path() ?></td>
    <td><?= $media->type() ?></td>
    <td><?= readablesize($media->size()) ?></td>
    <td><?= $media->width() ?></td>
    <td><?= $media->height() ?></td>
    <td><?= $media->length() ?></td>
    </tr>
    <?php
}


?>

</table>

<h1>Favicon</h1>

<table id="faviconlist">
<tr><th>id</th><th>extension</th><th>path</th><th>size</th><th>width</th><th>height</th></tr>

<?php
foreach ($faviconlist as $favicon) {
    ?>
    <tr>
    <td><?= $favicon->id() ?></td>
    <td><?= $favicon->extension() ?></td>
    <td><?= $favicon->path() ?></td>
    <td><?= readablesize($favicon->size()) ?></td>
    <td><?= $favicon->width() ?></td>
    <td><?= $favicon->height() ?></td>
    </tr>
    <?php
}


?>

</table>

</section>
</body>

<?php $this->stop('page') ?>