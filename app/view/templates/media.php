<?php $this->layout('layout', ['title' => 'media', 'css' => $css . 'home.css']) ?>


<?php $this->start('page') ?>

<body>

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'media']) ?>


<main class="media">


<div id="tree">
<h2>Explorer</h2>


<table id="dirlsit">
<tr><th>folder</th><th>files</th></tr>

<?php

function treecount(array $dir, string $dirname, int $deepness, string $path, string $currentdir, array $opt)
{
    if ($path === $currentdir) {
        $folder = '├─📂<strong>' . $dirname . '<strong>';
    } else {
        $folder = '├─📁' . $dirname;
    }
    echo '<tr>';
    echo '<td><a href="?path=' . $path . '&sortby=' . $opt['sortby'] . '&order=' . $opt['order'] . '">' . str_repeat('&nbsp;&nbsp;', $deepness) . $folder . '</a></td>';
    echo '<td>' . $dir['dirfilecount'] . '</td>';
    echo '</tr>';
    foreach ($dir as $key => $value) {
        if (is_array($value)) {
            treecount($value, $key, $deepness + 1, $path . DIRECTORY_SEPARATOR . $key, $currentdir, $opt);
        }
    }
}

treecount($dirlist, 'media', 0, 'media', $dir, $opt);

?>



</table>
</div>


<div id="explorer">


<h2><?= $dir ?></h2>


<details>
    <summary>Print this content on your page</summary>
    
    <p>
    <code>%MEDIA?path=<?= substr($dir, 6) ?>&sortby=<?= $opt['sortby'] ?>&order=<?= $opt['order'] ?>%</code>
    </p>

</details>



<form id="addfolder" action="<?= $this->url('mediafolder') ?>" method="post">
    <label for="foldername">📂 New folder</label>
    <input type="text" name="foldername" id="foldername" placeholder="folder name" required>
    <input type="hidden" name="dir" value="<?= $dir ?>">
    <input type="submit" value="create folder">
</form>

<form id=addmedia action="<?= $this->url('mediaupload') ?>" method="post" enctype="multipart/form-data">
    <label for="file">🚀 Upload file(s)</label>
    <input type='file' id="file" name='file[]' multiple required>
    <input type="hidden" name="dir" value="<?= $dir ?>">
    <input type="submit" value="upload">
</form>



<table id="medialist">
<tr>
    <th><a href="?path=<?= $dir ?>&sortby=id&order=<?php echo ($opt['order'] * -1); ?>">id</a></th>
    <th>ext</th>
    <th><a href="?path=<?= $dir ?>&sortby=type&order=<?php echo ($opt['order'] * -1); ?>">type</a></th>
    <th><a href="?path=<?= $dir ?>&sortby=size&order=<?php echo ($opt['order'] * -1); ?>">size</a></th>
    <th>width</th>
    <th>height</th>
    <th>lengh</th>
    <th>code</th>
</tr>

<?php
foreach ($medialist as $media) {
    ?>
    <tr>
    <td><a href="<?= $media->getfullpath() ?>" target="_blank"><?= $media->id() ?></a></td>
    <td><?= $media->extension() ?></td>
    <td><?= $media->type() == 'image' ? '<span class="thumbnail">image 👁<img src="' . $media->getfullpath() . '"></span>' : $media->type() ?></td>
    <td><?= $media->size('hr') ?></td>
    <td><?= $media->width() ?></td>
    <td><?= $media->height() ?></td>
    <td><?= $media->length() ?></td>
    <td class="code"><code>
    <?php
        if($media->type() == 'image') {
            ?>
            ![<?= $media->id() ?>](<?= $media->getincludepath() ?>)
            <?php
        } elseif ($media->type() == 'other') {
            ?>
            [<?= $media->id() ?>](<?= $media->getincludepath() ?>)
            <?php
        } else {
            echo $media->getincludepath();
        }
        ?>
    </code></td>
    </tr>
    <?php

}


?>

</table>

</div>

</main>
</body>

<?php $this->stop('page') ?>