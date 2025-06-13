<?php

$this->layout('layout', ['title' => 'URL management', 'stylesheets' => [$css . 'back.css']]) ?>


<?php $this->start('page') ?>

<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'home', 'pagelist' => $pagelist]) ?>


<main class="url">
    <section>
        <h2>Urls</h2>
        <div class="scroll">
            <table>
                <thead>
                    <th>
                        x
                    </th>
                    <th>
                        URL
                    </th>
                    <th>
                        response
                    </th>
                    <th>
                        last checked
                    </th>
                    <th>
                        expire
                    </th>
                    <th>
                        edit
                    </th>
                    <th>
                        re-check
                    </th>
                </thead>

                <?php foreach($urls as $url => $infos) : ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="" id="">
                        </td>
                        <td>
                            <a href="<?= $url ?>"><?= $url ?></a>
                        </td>
                        <td>
                            <?= $infos['response'] ?>
                        </td>
                        <td title="<?= $this->datemedium($infos['timestamp']) ?>">
                            <?= hrdi($infos['timestamp']->diff($now)) ?> ago
                        </td>
                        <td title="<?= $this->datemedium($infos['expire']) ?>">
                            in <?= hrdi($infos['expire']->diff($now)) ?>
                        </td>
                        <td>
                            <a href="" class="button">
                                <i class="fa fa-pencil"></i>
                            </a>
                        </td>
                        <td>
                            <a href="" class="button">
                                <i class="fa fa-refresh"></i>
                            </a>
                        </td>
                    </tr>    
                <?php endforeach ?>
            </table>
        </div>
    </section>
</main>

<?php $this->stop('page') ?>
