<?php $this->layout('layout', ['title' => 'logs', 'stylesheets' => [$css . 'back.css', $css . 'adminlog.css']]) ?>
<?php $this->start('page') ?>
<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'admin', 'pagelist' => $pagelist]) ?>

<main>
    <section>
        <h2>
            Logs
            <span>
                <a href="#bottom" title="jump to top"><i class="fa fa-arrow-circle-down"></i></a>
                <a href="#top" title="jump to bottom"><i class="fa fa-arrow-circle-up"></i></a>
            </span>
        </h2>
        <div id="options">
            <form class="level" action="<?= $this->url('adminlog') ?>#bottom" method="get">
                <input type="hidden" name="warn" value="0">
                <input type="checkbox" name="warn" id="warn" value="1" <?= $warn ? 'checked' : '' ?>>
                <label for="warn" class="warn">warn</label>

                <input type="hidden" name="error" value="0">
                <input type="checkbox" name="error" id="error" value="1" <?= $error ? 'checked' : '' ?>>
                <label for="error" class="error">error</label>

                <input type="hidden" name="info" value="0">
                <input type="checkbox" name="info" id="info" value="1" <?= $info ? 'checked' : '' ?>>
                <label for="info" class="info">info</label>

                <input type="submit" value="filter">
            </form>
        </div>
        <div class="scroll">
            <span id="top"></span>
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
            <span id="bottom"></span>
        </div>
    </section>
</main>

<?php $this->stop('page') ?>
