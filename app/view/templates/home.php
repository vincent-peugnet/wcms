<?php $this->layout('layout', ['title' => 'home', 'css' => $css . 'home.css']) ?>




<?php $this->start('page') ?>


<body>

    <?php $this->insert('backtopbar', ['user' => $user]) ?>

<?php if($user->iseditor()) { ?>

<section>



</div>


<div id="flex">
    
    
    <?php $this->insert('homeopt', ['opt' => $opt]) ?>



<div id="main">
<h2>Articles</h2>
<form action="/massedit" method="post">


    <div id="massedit">
        <h3>Mass Edit</h3>
    <select name="massedit" required>
        <option value="public">set as public</option>
        <option value="private">set as private</option>
        <option value="not published">set as not published</option>
        <option value="erasetag">erase all tags</option>
        <option value="erasetemplate">erase template</option>
        <option value="delete">delete</option>
        <option value="render">render</option>
    </select>

    <input type="submit" name="massaction" value="do" onclick="confirmSubmit(event, 'Are you sure')" >

    <input type="text" name="targettag" placeholder="add tag">
    <input type="submit" name="massaction" value="add tag" onclick="confirmSubmit(event, 'Are you sure')" >

    <select name="masstemplate">
        <?php
        foreach ($table2 as $art) {
            echo '<option value="' . $art->id() . '">' . $art->id() . '</option>';
        }
        ?>
    </select>

    <input type="submit" name="massaction" value="set template" onclick="confirmSubmit(event, 'Are you sure')" >

    <input type="hidden" name="action" value="massedit">
    </div>


        <table id="home2table">
        <tr><th>x</th><th>id</th><th>edit</th><th>see</th><th>del</th><th>log</th><th>tag</th><th>summary</th><th>↘ to</th><th>↗ from</th><th>last modification</th><th>date of creation</th><th>privacy</th></tr>
        <?php   foreach ($table2 as $item) { ?>
            <tr>
            <td><input type="checkbox" name="id[]"  value="<?= $item->id() ?>" id="<?= $item->id() ?>"></td>
            <td><label title="<?= $item->title() ?>" for="<?= $item->id() ?>"><?= $item->id() ?></label></td>
            <td><a href="<?= $this->uart('artedit', $item->id()) ?>">✏</a></td>
            <td><a href="<?= $this->uart('artread/', $item->id()) ?>" target="_blank">👁</a></td>
            <td><a href="<?= $this->uart('artdelete', $item->id()) ?>" >✖</a></td>
            <td><a href="<?= $this->uart('artlog', $item->id()) ?>" target="_blank">⁋</a></td>
            <td><a title="<?= $item->tag('string') ?>"><?= $item->tag('sort') ?></a></td>
            <td><?= $item->description() ?></td>
            <td><?= $item->linkto('sort') ?></td>
            <td><a title="<?= $item->linkfrom('string') ?>" ><?= $item->linkfrom('sort') ?></a></td>
            <td><?= $item->datemodif('hrdi') ?></td>
            <td><?= $item->datecreation('hrdi') ?></td>
            <td><?= $item->secure('string') ?></td>
            </tr>

      <?php  }?>
 </table>
</form>
</div>
</div>
</section>

<?php } ?>

</body>



<?php $this->stop() ?>