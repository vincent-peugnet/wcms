<?php $this->layout('alertlayout') ?>

<?php $this->start('alert') ?>

<style>

form {
  display: flex;
  flex-direction: column;
  max-width: 400px;
}
</style>

<form action="" method="post">
    <input type="hidden" name="origin" value="<?= $_POST['origin'] ?>">


    <label for="name">name</label>
    <input type="text" name="" id="name">
    
    
    <label for="email">email</label>
    <input type="email" name="" id="email">
    
    <label for="website">website</label>
    <input type="url" name="" id="website">

    <label for="comment">comment</label>
    <textarea name="comment" id="comment"><?= $this->e($post['comment']) ?></textarea>
    <p>
        <input type="submit" value="<?= $this->e($post['button']) ?>">
    </p>
</form>


<?php $this->stop() ?>

