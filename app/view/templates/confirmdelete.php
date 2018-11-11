<?php $this->layout('layout', ['title' => 'delete', 'description' => 'delete', 'css' => $css . 'delete.css']) ?>


<?php $this->start('page') ?>


<?php $this->insert('navart', ['user' => $user, 'art' => $art, 'artexist' => $artexist]) ?>

<div>

<h1>Delete</h1>

<ul>
<li>Id : <?= $art->title() ?></li>
<li>Title : <?= $art->title() ?></li>
<li>Article(s) linked to this one : <?= $art->linkto('sort') ?></li>
<li>Article(s) linked from this one : <?= $art->linkfrom('sort') ?></li>
<li>Number of edits : <?= $art->editcount() ?></li>
</ul>

<?php if (!empty($art->linkto())) { ?>

<h2>Article linked to :</h2>

<ul>
<?php foreach ($art->linkto('array') as $linkto) {
    echo '<li><a href="./?id=' . $linkto . '">' . $linkto . '</a></li>';
} ?>
</ul>

<?php 
} ?>

</div>


<form action="<?= $this->uart('artdelete', $art->id()) ?>" method="post">
<input type="hidden" name="deleteconfirm" value="true">
<input type="submit" value="confirm delete">
</form>



<?php $this->stop() ?>