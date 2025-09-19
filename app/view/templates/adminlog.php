<?php $this->layout('backlayout', ['title' => 'logs', 'stylesheets' => [$css . 'back.css', $css . 'adminlog.css'], 'theme' => $theme]) ?>
<?php $this->start('page') ?>
<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'admin', 'pagelist' => $pagelist]) ?>

<main>
    <section>
        <h2>
            Logs (<?= count($logs) ?>)
            <span>
                <a href="#bottom" title="jump to top"><i class="fa fa-arrow-circle-down"></i></a>
                <a href="#top" title="jump to bottom"><i class="fa fa-arrow-circle-up"></i></a>
            </span>
        </h2>
        <div id="options">
            <form action="<?= $this->url('adminlog') ?>#bottom" method="get">
                <input type="hidden" name="warn" value="0">
                <input type="checkbox" name="warn" id="warn" value="1" <?= $warn ? 'checked' : '' ?>>
                <label for="warn" class="level warn">warn</label>

                <input type="hidden" name="error" value="0">
                <input type="checkbox" name="error" id="error" value="1" <?= $error ? 'checked' : '' ?>>
                <label for="error" class="level error">error</label>

                <input type="hidden" name="info" value="0">
                <input type="checkbox" name="info" id="info" value="1" <?= $info ? 'checked' : '' ?>>
                <label for="info" class="level info">info</label>

                <input type="number" name="limit" id="limit" value="<?= $limit ?>" min="0">
                <label for="limit">last lines</label>

                <input type="submit" value="filter">
            </form>
            <a href="<?= $this->url('adminlogdownload') ?>" class="button" title="download original log file">
                <i class="fa fa-download"></i> <span class="text">download</span>
            </a>
        </div>
        <div class="scroll">
            <span id="top"></span>
            <table>
                <thead>
                    <tr>
                        <th>
                            line
                        </th>
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
                    <?php foreach($logs as $line => $log) : ?>
                    <tr>
                        <td class="line">
                            <?= $line ?>
                        </td>
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
<footer>
    w_error.log | file lines: <?= $filelines ?> | file size: <?= $this->readablesize($filesize) ?>o
</footer>

<?php $this->stop('page') ?>
