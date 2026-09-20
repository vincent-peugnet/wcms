<?php

$this->layout('backlayout', ['title' => 'Comments management', 'stylesheets' => [$css . 'back.css', $css . 'comment.css'], 'theme' => $theme]) ?>


<?php $this->start('page') ?>

<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'comment', 'pagelist' => $pagelist]) ?>


<main class="comment">
    <section>
        <h2>
            Comments
            (<?= count($comments) ?>)
        </h2>

        <div class="scroll">
            <table>
                <thead class="sticky">
                    <th>
                        id
                    </th>
                    <th>
                        message
                    </th>
                    <th>
                        author
                    </th>
                    <th>
                        website
                    </th>
                    <th>
                        <i class="fa fa-gavel"></i>
                    </th>
                    <th>
                        page
                    </th>
                    <th>
                        date
                    </th>
                </thead>
                <tbody>
                    <?php foreach ($comments as $id => $comment) : ?>
                        <tr>
                            <td>
                                <?= $id ?>
                            </td>
                            <td class="message"><?= $this->e($comment->message()) ?></td>
                            <td>
                                <?= $comment instanceof Wcms\Commentuser ? '<i class="fa fa-user"></i>' : '' ?>
                                <?= $this->e($comment->visiblename()) ?>
                            </td>
                            <td>
                                <?php if ($comment instanceof Wcms\Commentvisitor) : ?>
                                    <a target="_blank" href="<?= $comment->website() ?>"><?= ltrim(substr($comment->website(), 6), "\/") ?></a>
                                <?php endif ?>
                            </td>
                            <td>
                                <?php if ($comment->approved()) : ?>
                                    <i class="fa fa-check"></i>
                                <?php endif ?>
                            </td>
                            <td>
                                <?php $page = explode('#', $id)[0] ?>
                                <a href="<?= $this->upage('pageedit', $page) ?>" class="button">
                                    <?= $page ?>
                                </a>
                            </td>
                            <td>
                                <?= $comment->date('hrdi') ?> ago
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </section>

</main>

<?php $this->stop('page') ?>
