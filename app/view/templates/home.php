<?php $this->layout('layout', ['title' => 'home', 'css' => $css . 'home.css', 'favicon' => '']) ?>




<?php $this->start('page') ?>


<body>

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'home']) ?>

<?php if($user->iseditor()) { ?>

<main class="home">



</div>


<div id="flex">
    
    
    <?php $this->insert('homeopt', ['opt' => $opt]) ?>



<div id="main">
<h2>Pages</h2>
<form action="/massedit" method="post">


    <div id="massedit">
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

    <input type="hidden" name="action" value="massedit">
    </div>


        <table id="home2table">
        <tr>
        <th>x</th>
        <th><a href="<?= $opt->getadress('id') ?>">id</a></th>
        <th>edit</th>
        <th>see</th>
        <th class="delete">del</th>
        <th class="log">log</th>
        <th class="tag"><a href="<?= $opt->getadress('tag') ?>">tag</a></th>
        <th class="summary">summary</th>
        <th class="linkto"><a href="<?= $opt->getadress('linkto') ?>">to</a></th>
        <th class="linkfrom"><a href="<?= $opt->getadress('linkfrom') ?>">from</a></th>
        <th class="datemodif"><a href="<?= $opt->getadress('datemodif') ?>">last modification</a></th>
        <th class="datecreation"><a href="<?= $opt->getadress('datecreation') ?>">date of creation</a></th>
        <th class="date"><a href="<?= $opt->getadress('date') ?>">date</a></th>
        <th class="secure"><a href="<?= $opt->getadress('secure') ?>">privacy</a></th>
        </tr>
        <?php   foreach ($table2 as $item) { ?>
            <tr>
            <td><input type="checkbox" name="id[]"  value="<?= $item->id() ?>" id="<?= $item->id() ?>"></td>
            <td><label title="<?= $item->title() ?>" for="<?= $item->id() ?>"><?= $item->id() ?></label></td>
            <td><a href="<?= $this->uart('artedit', $item->id()) ?>">✏</a></td>
            <td><a href="<?= $this->uart('artread/', $item->id()) ?>" target="_blank">👁</a></td>
            <td class="delete"><a href="<?= $this->uart('artdelete', $item->id()) ?>" >✖</a></td>
            <td class="log"><a href="<?= $this->uart('artlog', $item->id()) ?>" target="_blank">⁋</a></td>
            <td class="tag"><a title="<?= $item->tag('string') ?>"><?= $item->tag('sort') ?></a></td>
            <td class="summary"><?= $item->description() ?></td>
            <td class="linkto"><a title="<?= $item->linkto('string') ?>" ><?= $item->linkto('sort') ?></a></td>
            <td class="linkfrom"><a title="<?= $item->linkfrom('string') ?>" ><?= $item->linkfrom('sort') ?></a></td>
            <td class="datemodif"><?= $item->datemodif('hrdi') ?></td>
            <td class="datecreation"><?= $item->datecreation('hrdi') ?></td>
            <td class="date"><?= $item->date('dmy') ?></td>
            <td class="secure"><?= $item->secure('string') ?></td>
            </tr>

      <?php  }?>
 </table>
</form>
</div>
</div>
</main>

<?php } ?>

</body>



<?php $this->stop() ?>