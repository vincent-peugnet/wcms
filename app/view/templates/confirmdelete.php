<?php $this->layout('layout', ['title' => 'delete', 'description' => 'delete', 'stylesheets' => [$css . 'home.css']]) ?>


<?php $this->start('page') ?>



<div>

<h1>Delete</h1>

<ul>
<li>Id : <?= $page->id() ?></li>
<li>Title : <?= $page->title() ?></li>
<li>Number of edits : <?= $page->editcount() ?></li>
</ul>



</div>


<form action="<?= $this->upage('pagedelete', $page->id()) ?>" method="post">
<input type="hidden" name="deleteconfirm" value="true">
<input type="submit" value="confirm delete">
</form>



<?php $this->stop() ?>