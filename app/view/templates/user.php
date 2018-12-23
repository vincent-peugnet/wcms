<?php $this->layout('layout', ['title' => 'user', 'css' => $css . 'home.css']) ?>


<?php $this->start('page') ?>

<body>

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'user']) ?>


<main class="user">

<table>
<tr>
<th>id</th><th>password</th><th>level</th><th>action</th>
</tr>

<tr>
    <form action="<?= $this->url('useradd') ?>" method="post">
    <td>
            <input type="text" name="id" required>
    </td>
    <td>
        <input type="password" name="password" minlength="4" maxlenght="64" required>
    </td>
    <td>
        <select name="level" id="level">
            <option value="1">reader</option>
            <option value="2">invite</option>
            <option value="3">editor</option>
            <option value="4">super editor</option>
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
    <form action="<?= $this->url('userupdate') ?>">

    <td>
    <?= $user->id() ?>
    </td>

    <td>
    <input type="password" name="password" placeholder="<?= str_repeat('°', $user->password('int')) ?>" min="4" max="64" required>
    </td>

    <td>
    <select name="level" id="level">
            <option value="1" <?= $user->level() === 1 ? 'selected' : '' ?>>reader</option>
            <option value="2" <?= $user->level() === 2 ? 'selected' : '' ?>>invite</option>
            <option value="3" <?= $user->level() === 3 ? 'selected' : '' ?>>editor</option>
            <option value="4" <?= $user->level() === 4 ? 'selected' : '' ?>>super editor</option>
    </select>
    </td>

    <td>
    <input type="submit" value="update">
    </form>

    <form action="<?= $this->url('userdelete') ?>" method="post">
    <input type="submit" value="delete">
    </form>
    </td>

    </tr>

    <?php
    }
?>

</table>

<?php var_dump($userlist); ?>
    

</main>
</body>

<?php $this->stop('page') ?>