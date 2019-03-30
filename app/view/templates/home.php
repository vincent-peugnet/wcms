<?php $this->layout('layout', ['title' => 'home', 'css' => $css . 'home.css', 'favicon' => '']) ?>




<?php $this->start('page') ?>


<body>

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'home']) ?>

<?php if($user->iseditor()) { ?>

<main class="home">



</div>

    
    
    <?php $this->insert('homeopt', ['opt' => $opt, 'user' => $user]) ?>

<div id="main">

<article id="main">

<h2>Pages</h2>

<details id="import">
    <summary>Import W JSON page file</summary>
    <i>Upload page file as json</i>
    <form action="<?=$this->url('artupload') ?>" method="post" enctype="multipart/form-data">
    <input type="file" name="pagefile" id="pagefile" accept=".json">
    <label for="pagefile">JSON Page file</label>
    <input type="hidden" name="erase" value="0">
    <input type="hidden" name="datecreation" value="0">
    </br>
    <input type="text" name="id" id="id" placeholder="new id (optionnal)">
    <label for="id">change ID</label>
    </br>
    <input type="checkbox" name="datecreation" id="datecreation" value="1">
    <label for="datecreation">Reset date creation as now</label>
    </br>
    <input type="checkbox" name="erase" id="erase" value="1">
    <label for="erase">Replace if already existing</label>
    </br>
    <input type="submit" value="upload">
    </form>
</details>



<form action="/massedit" method="post">

<div id="massedit">
    <!-- 
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
 -->



    <input type="hidden" name="action" value="massedit">
    </div>


        <table id="home2table">
        <thead>
        <tr>
        <th>x</th>
        <th><a href="<?= $opt->getadress('id') ?>">id</a></th>
        <th>edit</th>
        <th>see</th>
        <th class="delete" title="delete page">del</th>
        <?php if($user->issupereditor()) { ?>
        <th class="download" title="download page as json">dl</th>
        <?php } if($columns['tag']) { ?>
        <th class="tag"><a href="<?= $opt->getadress('tag') ?>">tag</a></th>
        <?php } if($columns['title']) { ?>
        <th class="title"><a href="<?= $opt->getadress('title') ?>">title</a></th>
        <?php } if($columns['description']) { ?>
        <th class="summary">summary</th>
        <?php } if($columns['linkto']) { ?>
        <th class="linkto"><a href="<?= $opt->getadress('linkto') ?>">to</a></th>
        <?php } if($columns['linkfrom']) { ?>
        <th class="linkfrom"><a href="<?= $opt->getadress('linkfrom') ?>">from</a></th>
        <?php } if($columns['datemodif']) { ?>
        <th class="datemodif"><a href="<?= $opt->getadress('datemodif') ?>">last modification</a></th>
        <?php } if($columns['datecreation']) { ?>
        <th class="datecreation"><a href="<?= $opt->getadress('datecreation') ?>">date of creation</a></th>
        <?php } if($columns['date']) { ?>
        <th class="date"><a href="<?= $opt->getadress('date') ?>">date</a></th>
        <?php } if($columns['secure']) { ?>
        <th class="secure"><a href="<?= $opt->getadress('secure') ?>">privacy</a></th>
        <?php } if($columns['visitcount']) { ?>
        <th class="visitcount"><a href="<?= $opt->getadress('visitcount') ?>">visit</a></th>
        <?php } if($columns['editcount']) { ?>
        <th class="editcount"><a href="<?= $opt->getadress('editcount') ?>">edit</a></th>
        <?php } if($columns['affcount']) { ?>
        <th class="affcount"><a href="<?= $opt->getadress('affcount') ?>">aff</a></th>
        <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php   foreach ($table2 as $item) { ?>
            <tr>
            <td><input type="checkbox" name="id[]"  value="<?= $item->id() ?>" id="id_<?= $item->id() ?>"></td>
            <td><label title="<?= $item->title() ?>" for="id_<?= $item->id() ?>"><?= $item->id() ?></label></td>
            <td><a href="<?= $this->uart('artedit', $item->id()) ?>">✏</a></td>
            <td><a href="<?= $this->uart('artread/', $item->id()) ?>" target="_blank">👁</a></td>
            <td class="delete"><a href="<?= $this->uart('artdelete', $item->id()) ?>" >✖</a></td>
            <?php if($user->issupereditor()) { ?>
            <td><a href="<?= $this->uart('artdownload', $item->id()) ?>" download>↓</a></td>
            <?php } if($columns['tag']) { ?>
            <td class="tag"><a title="<?= $item->tag('string') ?>"><?= $item->tag('sort') ?></a></td>
            <?php } if($columns['title']) { ?>
            <td class="title" title="<?= $item->title() ?>"><?= $item->title() ?></td>
            <?php } if($columns['description']) { ?>
            <td class="summary" title="<?= $item->description() ?>"><?= $item->description('short') ?></td>
            <?php } if($columns['linkto']) { ?>
            <td class="linkto"><a title="<?= $item->linkto('string') ?>" ><?= $item->linkto('sort') ?></a></td>
            <?php } if($columns['linkfrom']) { ?>
            <td class="linkfrom"><a title="<?= $item->linkfrom('string') ?>" ><?= $item->linkfrom('sort') ?></a></td>
            <?php } if($columns['datemodif']) { ?>
            <td class="datemodif"><?= $item->datemodif('hrdi') ?></td>
            <?php } if($columns['datecreation']) { ?>
            <td class="datecreation"><?= $item->datecreation('hrdi') ?></td>
            <?php } if($columns['date']) { ?>
            <td class="date"><?= $item->date('dmy') ?></td>
            <?php } if($columns['secure']) { ?>
            <td class="secure"><?= $item->secure('string') ?></td>
            <?php } if($columns['visitcount']) { ?>
            <td class="visitcount"><?= $item->visitcount() ?></td>
            <?php } if($columns['editcount']) { ?>
            <td class="editcount"><?= $item->editcount() ?></td>
            <?php } if($columns['affcount']) { ?>
            <td class="affcount"><?= $item->affcount() ?></td>
            <?php } ?>
            </tr>

      <?php  }?>
            </tbody>
 </table>
</form>
</article>
</div>
</main>

<?php } ?>

</body>



<?php $this->stop() ?>