<?php $id = $page->id() ?>
<?php $this->layout('modallayout', ['theme' => $theme, 'title' => "Delete page $id", 'description' => 'delete', 'css' => $css, 'user' => $user, 'pagelist' => $pagelist]) ?>

<?php $this->start('modal') ?>

<p>URL : <a href="<?= $this->upage('pageread', $page->id()) ?>" ><?= $this->upage('pageread', $page->id()) ?></a></p>
<p>Id : <?= $page->id() ?></p>
<p>Title : <?= $this->e($page->title()) ?></p>
<p>Number of edits : <?= $page->editcount() ?></p>
<p>Number of displays : <?= $page->displaycount() ?></p>
<p>Comments : <?= $page->commentcount() ?></p>
<p>
    Page linking to this one : <?= $pageslinkingtocount ?>
</p>
<?php if($page->commentcount() > 0) : ?>
    <p>
        <i class="fa fa-warning"></i>
        Deleting the page will also delete all the comments.
    </p>
<?php endif ?>
<?php if ($pageslinkingtocount > 0) : ?>
    <p>
        <a class="button" href="<?= $this->url('home', [], '?linkto=' . $page->id() . '&submit=filter') ?>" title="search for pages linking to this one in home view">
            <i class="fa fa-search"></i>
            explore backlinks
        </a>
    </p>
<?php endif ?>

<form action="<?= $this->upage('pagedelete', $page->id()) ?>" method="post">

    <?php if ($cancelroute === 'pageread') : ?>
        <a href="<?= $this->upage('pageread', $page->id()) ?>" class="button" autofocus>
            <i class="fa fa-times"></i> Cancel
        </a>
    <?php elseif ($cancelroute === 'home') : ?>
        <a href="<?= $this->url('home') ?>" class="button" autofocus>
            <i class="fa fa-times"></i> Cancel
        </a>
    <?php endif ?>

    <input type="hidden" name="deleteconfirm" value="true">
    <button type="submit">
        <i class="fa fa-trash"></i>
        <span class="text">Confirm delete</span>
    </button>
</form>

<?php $this->stop() ?>
