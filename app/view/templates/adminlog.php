<?php $this->layout('layout', ['title' => 'admin', 'stylesheets' => [$css . 'back.css', $css . 'adminlog.css']]) ?>
<?php $this->start('page') ?>
<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'admin', 'pagelist' => $pagelist]) ?>

<main>
    <section>
        <h2>
            Logs
        </h2>
        <div class="scroll">
            <table>
                <thead>
                    <tr>
                        <th>
                            date
                        </th>
                        <th>
                            level
                        </th>
                        <th>
                            message
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($logs as $log) : ?>
                    <tr>
                        <td class="date">
                            <?= $this->datemedium($log->date) ?>
                        </td>
                        <td class="level">
                        <div class="<?= $log->level ?>">
                                <?= $log->level ?>
                        </div> 
                        </td>
                        <td>
                            <?= $log->message ?>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php $this->stop('page') ?>
