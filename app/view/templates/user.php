<?php $this->layout('layout', ['title' => 'user', 'css' => $css . 'home.css']) ?>


<?php $this->start('page') ?>

<body>

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'user']) ?>


<main class="user">

<section>

<article>

<h1>User : <?= $user->id() ?></h1>



<form action="<?= $this->url('userpref') ?>" method="post">

<h2>Preferences</h2>

<input type="number" name="cookie" value="<?= $getuser->cookie() ?>" id="cookie" min="0" max="365">
<label for="cookie">Cookie conservation time <i>(In days)</i></label>

<input type="submit" value="submit">

</form>

</article>


<?php if($user->isadmin()) { ?>

<article>

<h1>Admin panel</h1>

<table>
<tr>
<th>id</th><th>password</th><th>hash</th><th>level</th><th>action</th>
</tr>

<tr>
    <form action="<?= $this->url('useradd') ?>" method="post">
    <td>
            <input type="text" name="id" maxlength="128" required>
    </td>
    <td>
        <input type="password" name="password" minlength="4" maxlength="64" required>
    </td>

    <td>
    <input type="hidden" name="passwordhashed" value="0">
    <input type="checkbox" name="passwordhashed" value="1">
    </td>

    <td>
        <select name="level" id="level">
            <option value="1">reader</option>
            <option value="2">invite</option>
            <option value="3">editor</option>
            <option value="4">super editor</option>
            <option value="10">admin</option>
        </select>
    </td>
    <td>
        <input type="submit" value="add">
    </td>
    </form>
</tr>


<?php
foreach ($userlist as $user ) {
    ?>
    
    <tr>
    <form action="<?= $this->url('userupdate') ?>" method="post">

    <td>
    <?= $user->id() ?>
    </td>

    <td>
    <input type="password" name="password" minlength="4" maxlength="64" >
    </td>

    <td>
    <?= $user->passwordhashed() ? '🔑' : '<input type="hidden" name="passwordhashed" value="0"><input type="checkbox" name="passwordhashed" value="1">' ?>
    </td>

    <td>
    <select name="level" id="level">
            <option value="1" <?= $user->level() === 1 ? 'selected' : '' ?>>reader</option>
            <option value="2" <?= $user->level() === 2 ? 'selected' : '' ?>>invite</option>
            <option value="3" <?= $user->level() === 3 ? 'selected' : '' ?>>editor</option>
            <option value="4" <?= $user->level() === 4 ? 'selected' : '' ?>>super editor</option>
            <option value="10" <?= $user->level() === 10 ? 'selected' : '' ?>>admin</option>
    </select>
    </td>

    <td>
    <input type="hidden" name="id" value="<?= $user->id() ?>">
    <input type="submit" name="action" value="update">
    <input type="submit" name="action" value="delete">
    </form>

    </td>

    </tr>

    <?php
    }
?>

</table>

</article>

<?php } ?>


</section>


</main>
</body>

<?php $this->stop('page') ?>