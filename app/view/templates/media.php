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



<form id="folderadd" action="<?= $this->url('mediafolderadd') ?>" method="post">
    <label for="foldername">📂 New folder</label>
    <input type="text" name="foldername" id="foldername" placeholder="folder name" required>
    <input type="hidden" name="dir" value="<?= $dir ?>">
    <input type="submit" value="create folder">
</form>

<?php if($user->issupereditor()) { ?>

<form action="<?= $this->url('mediafolderdelete') ?>" id="deletefolder" method="post" class="hidephone">
    <input type="hidden" name="dir" value="<?= $dir ?>/">
    <input type="checkbox" name="deletefolder" id="confirmdeletefolder" value="1">
    <label for="confirmdeletefolder">Delete folder and all it's content</label>
    <input type="submit" value="delete folder" >
</form>


<?php } ?>

<form id=addmedia action="<?= $this->url('mediaupload') ?>" method="post" enctype="multipart/form-data">
    <label for="file">🚀 Upload file(s)</label>
    <input type='file' id="file" name='file[]' multiple required>
    <input type="hidden" name="dir" value="<?= $dir ?>">
    <input type="submit" value="upload">
</form>



<?php if($user->issupereditor()) { ?>

<form action="<?= $this->url('mediaedit') ?>" method="post" id="mediaedit">
    <input type="hidden" name="path" value="<?= $dir ?>">
    <label for="moveto">Selected medias :</label>
    <select name="dir" id="moveto" >
        <option selected>---select destination---</option>
        <option value="<?= Model::MEDIA_DIR ?>">/</option>
        <?php
            foreach ($pathlist as $path) {
                echo '<option value="' . Model::MEDIA_DIR . $path . '">' . $path . '</option>';
            }
        ?>
    </select>
    <input type="submit" name="action" value="move" >
    <input type="submit" name="action" value="delete" >
</form>

<?php } ?>




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
    ?>
    <tr>
    <td><input type="checkbox" name="id[]" value="<?= $media->getfulldir() ?>" form="mediaedit" id="media_<?= $media->id() ?>"></td>
    <td><label for="media_<?= $media->id() ?>"><?= $media->id() ?></label></td>    
    <td><?= $media->extension() ?></td>
    <td><a href="<?= $media->getfullpath() ?>" target="_blank"><?= $media->type() == 'image' ? '<span class="thumbnail">image 👁<img src="' . $media->getfullpath() . '"></span>' : $media->type() ?></a></td>
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