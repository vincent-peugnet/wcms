<?php

$this->layout('layout', ['title' => 'user', 'stylesheets' => [$css . 'back.css', $css . 'user.css']]) ?>


<?php $this->start('page') ?>

<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'user', 'pagelist' => $pagelist]) ?>


<main class="user">

    <section class="new-user">
        
        <h2>Add new user</h2>

        <form action="<?= $this->url('useradd') ?>" method="post" class="flexrow">

            <p class="field">
                <label for="id">Username</label>
                <input type="text" name="id" id="id" maxlength="<?= Wcms\Model::MAX_ID_LENGTH ?>" required>
            </p>
            <p class="field">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" minlength="<?= Wcms\Model::PASSWORD_MIN_LENGTH ?>" maxlength="<?= Wcms\Model::PASSWORD_MAX_LENGTH ?>" required>
            </p>
            <p class="field">
                <input type="hidden" name="passwordhashed" value="0">
                <label for="passwordhashed">Hash password</label>
                <input type="checkbox" name="passwordhashed" id="passwordhashed" value="1" checked>
            </p>
            <p class="field">
                <label for="level">Level</label>
                <select name="level" id="level">
                    <option value="1">reader</option>
                    <option value="2">invite</option>
                    <option value="3">editor</option>
                    <option value="4">super editor</option>
                    <option value="10">admin</option>
                </select>
            </p>
            <p class="field">
                <label for="expiredate">Expiration date</label>
                <input type="date" name="expiredate" id="expiredate" min="<?= $now->format('Y-m-d'); ?>">
            </p>
            <p class="field submit-field">
                <input type="submit" value="add">
            </p>
        </form>

    </section>

    <section class="all-users">

        <h2>Users</h2>

        <table>
            <tr>
                <th>id</th><th>password</th><th>hash</th><th>level</th><th>set expiration date</th><th>action</th><th>expire</th><th>connect</th>
            </tr>
            <?php foreach ($userlist as $user ) : ?>
                    
                <tr>
                    <form action="<?= $this->url('useredit') ?>" method="post">

                        <td>
                            <?= $user->id() ?>
                        </td>

                        <td>
                            <?php if ($user->isldap()) : ?>
                                LDAP auth
                            <?php else : ?>
                                <input type="password" name="password" minlength="<?= Wcms\Model::PASSWORD_MIN_LENGTH ?>" maxlength="<?= Wcms\Model::PASSWORD_MAX_LENGTH ?>" >
                            <?php endif ?>
                        </td>

                        <td>
                            <?php if (!$user->isldap()) : ?>
                                <?= $user->passwordhashed() ? '<i class="fa fa-key"></i>' : '<input type="hidden" name="passwordhashed" value="0"><input type="checkbox" name="passwordhashed" value="1">' ?>
                            <?php endif ?>
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

                        <td class="field nowrap">                            
                            <label><input type="checkbox" name="expiredate" id="expiredate" value="null"> reset</label>   
                            <input type="date" name="expiredate" id="expiredate" <?= $user->expiredate() !== false ?  'value="' . $user->expiredate('string') . '"' : '' ?>  min="<?= $now->format('Y-m-d'); ?>">                    
                        </td>

                        <td>
                            <input type="hidden" name="id" value="<?= $user->id() ?>">
                            <input type="submit" name="action" value="update">
                            <input type="submit" name="action" value="delete">
                        </td>

                        <td>
                            <?= $user->expiredate('hrdi') ?>
                        </td>

                        <td>
                            <?= $user->connectcount() ?>
                        </td>

                    </form>
                </tr>

            <?php endforeach ?>

        </table>

    </section>

</main>

<?php $this->stop('page') ?>
