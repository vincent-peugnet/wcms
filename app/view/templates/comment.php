<?php

$this->layout('backlayout', ['title' => 'Comments management', 'stylesheets' => [$css . 'back.css', $css . 'comment.css'], 'theme' => $theme]) ?>


<?php $this->start('page') ?>

<?php $this->insert('backtopbar', ['user' => $user, 'tab' => 'comment', 'pagelist' => $pagelist]) ?>

<?php $this->insert('commentmenu'); ?>

<main class="comment">
<aside id="filter" class="toggle-panel-container">
        <input id="showcommentfilterpanel" name="showcommentfilterpanel" value="1" class="toggle-panel-toggle" type="checkbox" form="workspace-form" <?= $workspace->showcommentfilterpanel() === true ? 'checked' : '' ?>>
        <label for="showcommentfilterpanel" class="toggle-panel-label"><span><i class="fa fa-filter"></i></span></label>
        <div class="toggle-panel" id="filterpanel">
            <h2>Filter</h2>   
            <div class="toggle-panel-content">
                <form action="" method="get" class="flexcol">
                    <fieldset class="flexcol">
                        <legend>Sort</legend>
                        <p class="field">
                            <label for="sortby">Sort by</label>    
                            <select name="sortby" id="sortby">
                                <option value="date" <?= $sortby === 'date' ? 'selected' : '' ?>>date</option>
                                <option value="approved" <?= $sortby === 'approved' ? 'selected' : '' ?>>approved</option>
                                <option value="visiblename" <?= $sortby === 'visiblename' ? 'selected' : '' ?>>author</option>
                            </select>
                        </p>
                        <p class="field">
                            <label for="asc">ascending</label>
                            <input type="radio" name="order" id="asc" value="1" <?= $order === 1 ? 'checked' : '' ?>>
                        </p>
                        <p class="field">
                            <label for="desc">descending</label>
                            <input type="radio" name="order" id="desc" value="-1" <?= $order === -1 ? 'checked' : '' ?>>
                        </p>
                    </fieldset>
                    <p class="field submit-field">
                        <input type="submit" value="filter">
                    </p>
                </form>
            </div>
        </div>
    </aside>

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

<?php if(!Wcms\Config::disablejavascript()) : ?>
    <script type="module" src="<?= Wcms\Model::jspath() ?>comment.bundle.js"></script>
<?php endif ?>

<?php $this->stop('page') ?>
