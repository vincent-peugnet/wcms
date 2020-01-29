<?php $this->layout('layout', ['title' => 'delete', 'description' => 'delete', 'stylesheets' => [$css . 'home.css']]) ?>


<?php $this->start('page') ?>



<div>

<h1>Delete</h1>

<ul>
<li>Id : <?= $page->id() ?></li>
<li>Title : <?= $page->title() ?></li>
<li>Article(s) linked to this one : <?= $page->linkto('sort') ?></li>
<li>Article(s) linked from this one : <?= $page->linkfrom('sort') ?></li>
<li>Number of edits : <?= $page->editcount() ?></li>
</ul>

<?php if (!empty($page->linkto())) { ?>

<h2>Article linked to :</h2>

<ul>
<?php foreach ($page->linkto('array') as $linkto) {
    echo '<li><a href="./?id=' . $linkto . '">' . $linkto . '</a></li>';
} ?>
</ul>

<?php 
} ?>

</div>


<form action="<?= $this->upage('pagedelete', $page->id()) ?>" method="post">
<input type="hidden" name="deleteconfirm" value="true">
<input type="submit" value="confirm delete">
</form>



<?php $this->stop() ?>