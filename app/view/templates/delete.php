<?php $this->layout('layout', ['title' => 'delete', 'description' => 'delete', 'stylesheets' => [$css . 'home.css', $css . 'back.css']]) ?>


<?php $this->start('page') ?>



<div class="block">

    <h1>Delete</h1>

    <ul>
        <li>Id : <?= $page->id() ?></li>
        <li>Title : <?= $page->title() ?></li>
        <li>Number of edits : <?= $page->editcount() ?></li>
        <li>Number of displays : <?= $page->displaycount() ?></li>
        <li>
            Page linking to this one : <?= $pageslinkingtocount ?>
            <?php if ($pageslinkingtocount > 0) : ?>
                <a href="<?= $this->url('home', [], '?linkto=' . $page->id() . '&submit=filter') ?>" title="search for pages linking to this one in home view">
                    <i class="fa fa-search"></i>
                </a>
            <?php endif ?>
        </li>
    </ul>
    



    
    
    <form action="<?= $this->upage('pagedelete', $page->id()) ?>" method="post">
        <input type="hidden" name="deleteconfirm" value="true">
        <input type="submit" value="confirm delete">
    </form>

    
</div>
<a href="<?= $this->upage('pageread', $page->id()) ?>">back to page</a>


<?php $this->stop() ?>
