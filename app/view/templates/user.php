<?php $this->layout('layout', ['title' => 'user', 'stylesheets' => [$css . 'home.css']]) ?>


<?php $this->start('page') ?>

<body>

    <?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'user', 'pagelist' => $pagelist]) ?>


<main class="user">

    <section id="pref">
        <div class="block">

            
            <h1>User : <?= $user->id() ?></h1>
            
            <div class="scroll">


                <h2>Infos</h2>


                <p>Connections count : <?= $getuser->connectcount() ?></p>

                <p>Account will expire in : <?= $getuser->expiredate('hrdi') ?></p>



                <h2>Preferences</h2>


                <form action="<?= $this->url('userpref') ?>" method="post">

                <p>
                    <input type="number" name="cookie" value="<?= $getuser->cookie() ?>" id="cookie" min="0" max="365">
                    <label for="cookie">Cookie conservation time <i>(In days)</i></label>
                    <p>When you tick the <em>remember-me</em> checkbox during login, you can choose how much time <strong>W</strong> will remember you.</p>
                    <input type="submit" value="submit">
                </p>
                    
                </form>




                <h2>Sessions Tokens</h2>

                <ul>

                <?php foreach ($tokenlist as $token ) {
                    ?>
                    <li >
                        <code>
                            ip : <?= $token->ip ?> | date : <?= $token->date['date'] ?> | conservation : <?= $token->conservation ?> days | user agent : <?= $token->useragent ?>
                        </code>
                        <form action="<?= $this->url('usertoken') ?>" method="post">
                        <input type="hidden" name="tokendelete" value="<?= $token->getId() ?>" >
                        <input type="submit" value="delete">
                        </form>

                    </li>
                <?php
                } ?>
                </ul>

            </div>


        </div>

    </section>

    
    <?php if($user->isadmin()) { ?>
    
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

    <?php } ?>



</main>
</body>

<?php $this->stop('page') ?>