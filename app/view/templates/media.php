<?php $this->layout('layout', ['title' => 'media', 'css' => $css . 'home.css']) ?>


<?php $this->start('page') ?>

<body>

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'media', 'pagelist' => $pagelist]) ?>

    <?php $this->insert('mediamenu', ['dir' => $dir, 'user' => $user, 'pathlist' => $pathlist, 'opt' => $opt]) ?>

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


<h2>filter</h2>

<form action="" method="get">
                <ul>
                    <li><input type="checkbox" name="type[]" id="image" value="image" <?= in_array('image', $opt['type']) ? 'checked' : '' ?>><label for="image">image</label></li>
                    <li><input type="checkbox" name="type[]" id="sound" value="sound" <?= in_array('sound', $opt['type']) ? 'checked' : '' ?>><label for="sound">sound</label></li>
                    <li><input type="checkbox" name="type[]" id="video" value="video" <?= in_array('video', $opt['type']) ? 'checked' : '' ?>><label for="video">video</label></li>
                    <li><input type="checkbox" name="type[]" id="other" value="other" <?= in_array('other', $opt['type']) ? 'checked' : '' ?>><label for="other">other</label></li>
                </ul>
                <select name="" id="">
                    <option value="id" <?= $opt['sortby'] === 'id' ? 'selected' : '' ?>>id</option>
                    <option value="type" <?= $opt['sortby'] === 'type' ? 'selected' : '' ?>>type</option>
                    <option value="size" <?= $opt['sortby'] === 'size' ? 'selected' : '' ?>>size</option>
                </select>
                </br>
                <input type="radio" name="order" id="asc" value="1" <?= $opt['order'] == 1 ? 'checked' : '' ?>><label for="asc">ascending</label>
                </br>
                <input type="radio" name="order" id="desc" value="-1" <?= $opt['order'] == -1 ? 'checked' : '' ?>><label for="desc">descending</label>
                </br>
                <input type="hidden" name="path" value="<?= $dir ?>">
                <input type="submit" value="filter">
            </form>

</div>




<div id="explorer">


<h2><?= $dir ?></h2>



<table id="medialist">
<tr>
    <th>x</th>
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
    if(in_array($media->type(), $opt['type'])) {
    ?>
    <tr>
    <td><input type="checkbox" name="id[]" value="<?= $media->getfulldir() ?>" form="mediaedit" id="media_<?= $media->id() ?>"></td>
    <td><label for="media_<?= $media->id() ?>"><?= $media->id() ?></label></td>    
    <td><?= $media->extension() ?></td>
    <td><a href="<?= $media->getfullpath() ?>" target="_blank"><?= $media->type() == 'image' ? '<span class="thumbnail">image 👁<img src="' . $media->getfullpath() . '"></span>' : '⧉ ' . $media->type() ?></a></td>
    <td><?= $media->size('hr') ?></td>
    <td><?= $media->width() ?></td>
    <td><?= $media->height() ?></td>
    <td><?= $media->length() ?></td>
    <td class="code"><code><?= $media->getcode() ?></code></td>
    </tr>
    <?php
    }

}


?>

</table>

</div>

</main>
</body>

<?php $this->stop('page') ?>