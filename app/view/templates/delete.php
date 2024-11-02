<?php $this->layout('layout', ['title' => 'delete', 'description' => 'delete', 'stylesheets' => [$css . 'home.css', $css . 'back.css']]) ?>

<?php $this->start('page') ?>

<h1>Delete page “<?= $page->id() ?>”&#8239;</h1>

<div class="confirm-delete">

    <p>URL : <a href="<?= $this->upage('pageread', $page->id()) ?>" ><?= $this->upage('pageread', $page->id()) ?></a></p>
    <p>Id : <?= $page->id() ?></p>
    <p>Title : <?= $page->title() ?></p>
    <p>Number of edits : <?= $page->editcount() ?></p>
    <p>Number of displays : <?= $page->displaycount() ?></p>
    <p>
        Page linking to this one : <?= $pageslinkingtocount ?>
        <?php if ($pageslinkingtocount > 0) : ?>
            <a class="button" href="<?= $this->url('home', [], '?linkto=' . $page->id() . '&submit=filter') ?>" title="search for pages linking to this one in home view">
                <i class="fa fa-search"></i>
            </a>
        <?php endif ?>
    </p>   

    <form action="<?= $this->upage('pagedelete', $page->id()) ?>" method="post">
        <input type="hidden" name="deleteconfirm" value="true">
        <button type="submit">
            <i class="fa fa-trash"></i>
            <span class="text">Confirm delete</span>
        </button>

        <?php if ($cancelroute === 'pageread') : ?>
            <a href="<?= $this->upage('pageread', $page->id()) ?>" class="button">
                <i class="fa fa-times"></i> Cancel
            </a>
        <?php elseif ($cancelroute === 'home') : ?>
            <a href="<?= $this->url('home') ?>" class="button">
                <i class="fa fa-times"></i> Cancel
            </a>
        <?php endif ?>
    </form>

</div>

<?php $this->stop() ?>
