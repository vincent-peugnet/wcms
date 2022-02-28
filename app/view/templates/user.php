<?php

use Wcms\Model;

$this->layout('layout', ['title' => 'user', 'stylesheets' => [$css . 'home.css']]) ?>


<?php $this->start('page') ?>

<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'user', 'pagelist' => $pagelist]) ?>


<main class="user">
    
    <section>
        
        <div class="block">

            <h1>Admin panel</h1>
            
            <div class="scroll">

                <table>
                <tr>
                <th>id</th><th>password</th><th>hash</th><th>level</th><th>set expiration date</th><th>action</th><th>expire</th><th>connect</th>
                </tr>

                <tr>
                    <form action="<?= $this->url('useradd') ?>" method="post">
                    <td>
                            <input type="text" name="id" maxlength="<?= Wcms\Model::MAX_ID_LENGTH ?>" required>
                    </td>
                    <td>
                        <input type="password" name="password" id="password" minlength="<?= Wcms\Model::PASSWORD_MIN_LENGTH ?>" maxlength="<?= Wcms\Model::PASSWORD_MAX_LENGTH ?>" required>
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
                        <input type="date" name="expiredate" id="expiredate" min="<?= $now->format('Y-m-d'); ?>">
                    </td>
                    <td>
                        <input type="submit" value="add">
                    </td>
                    <td>

                    </td>
                    <td>

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
                    <input type="password" name="password" minlength="<?= Wcms\Model::PASSWORD_MIN_LENGTH ?>" maxlength="<?= Wcms\Model::PASSWORD_MAX_LENGTH ?>" >
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
                        <input type="date" name="expiredate" id="expiredate"<?= $user->expiredate() !== false ?  'value="' . $user->expiredate('string') . '"' : '' ?>  min="<?= $now->format('Y-m-d'); ?>">
                        <span>reset<input type="checkbox" name="expiredate" id="expiredate" value="null"></span>
                        
                    </td>

                    <td>
                    <input type="hidden" name="id" value="<?= $user->id() ?>">
                    <input type="submit" name="action" value="update">
                    <input type="submit" name="action" value="delete">
                    </form>

                    </td>



                    <td>
                        <?= $user->expiredate('hrdi') ?>
                    </td>

                    <td>
                        <?= $user->connectcount() ?>
                    </td>


                    </tr>

                    <?php
                    }
                ?>

                </table>


            </div>

        </div>


    </section>


</main>

<?php $this->stop('page') ?>