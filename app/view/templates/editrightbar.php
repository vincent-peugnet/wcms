<aside id="rightbar" class="toggle-panel-container">
    <input id="showeditorrightpanel" name="showeditorrightpanel" value="1" class="toggle-panel-toggle" type="checkbox" <?= $workspace->showeditorrightpanel() === true ? 'checked' : '' ?> form="workspace-form" >
    <label for="showeditorrightpanel" class="toggle-panel-label"><span><i class="fa fa-info"></i></span></label>

    <div class="toggle-panel" id="rightbarpanel">

        <h1>Infos</h1>
    
        <div class="toggle-panel-content">
            <details id="stats" <?= $workspace->collapsemenu() ? '' : 'open' ?>>
                <summary>Stats</summary>

                <table>
                    <tbody>
                        <tr>
                            <td>edition</td>
                            <td><?= $page->editcount() ?></td>
                        </tr>
                        <tr>
                            <td>display</td>
                            <td><?= $page->displaycount() ?></td>
                        </tr>
                        <tr>
                            <td>visit</td>
                            <td><?= $page->visitcount() ?></td>
                        </tr>
                    </tbody>
                </table>

                <p class="field">
                    <a href="<?= $this->url('home', [], $homebacklink) ?>" class="button">
                        <i class="fa fa-list-ul"></i>
                        list pages that link here
                    </a>
                </p>
            </details>


            <details id="internal-links" <?= $workspace->collapsemenu() ? '' : 'open' ?>>
                <summary>internal links (<?= count($page->linkto()) ?>)</summary>

                <table>
                    <tbody>
                        <?php foreach ($page->linkto() as $link) : ?>
                            <tr>
                                <td class="id" <?= strlen($link) > 30 ? "title=\"$link\"" : '' ?>>
                                    <?= $link ?>
                                </td>
                                <td class="read">
                                    <a href="<?= $this->upage('pageread', $link) ?>" class="button" target="_blank">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                                <td class="edit">
                                    <a href="<?= $this->upage('pageedit', $link) ?>" class="button">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>

            </details>

            <details id="external-links" <?= $workspace->collapsemenu() ? '' : 'open' ?>>
                <summary>external links (<?= count($page->externallinks()) ?>)</summary>

                <ul class="externallinks">
                    <?php foreach ($page->externallinks() as $url => $status) : ?>
                        <?php if(Wcms\Config::urlchecker() && key_exists($url, $urls)) : ?>
                            <li title="🔗 <?= $url ?>&#013;
<?= $urls[$url]->response < 100 ? '❌' : '💡 ' . $urls[$url]->response  ?> <?= $urls[$url]->message ?>&#013;
⏱️ checked <?= hrdi($urls[$url]->timestampdate()->diff($now)) ?> ago&#013;
⏳️ expire in <?= hrdi($urls[$url]->expiredate()->diff($now)) ?>"
                            >
                        <?php else : ?>
                            <li title="<?= $url ?>">
                        <?php endif ?>
                            <a href="<?= $url ?>" target="_blank">
                                <?= ltrim(substr($url, 6), "\/") ?>
                            </a>
                            <span>
                                <?= is_bool($status) ? ($status ? '✅' : '💀') : '⏳️' ?>
                            </span>
                        </li>
                    <?php endforeach ?>
                </ul>
            </details>

            <details id="comments" <?= $workspace->collapsemenu() ? '' : 'open' ?>>
                <summary>comments (<?= count($comments) ?>)</summary>
                <?php if(count($comments) > 0) : ?>
                    <form action="<?= $this->url('pagecommentmoderation', ['page' => $page->id()]) ?>" method="post">
                        <button type="submit">
                            <i class="fa fa-gavel"></i>
                            apply comment moderation
                        </button>
                        <ul>
                            <?php foreach($comments as $id => $comment) : ?>
                                <li class="comment">
                                    <?= $comment instanceof Wcms\Commentuser ? '<i class="fa fa-user"></i>' : '' ?>
                                    <strong class="username"><?= $this->e($comment->visiblename()) ?></strong>
                                    <?php if ($comment instanceof Wcms\Commentvisitor) : ?>
                                        <a target="_blank" href="<?= $comment->website() ?>"><?= ltrim(substr($comment->website(), 6), "\/") ?></a>
                                    <?php endif ?>
                                    <span class="id"><?= $id ?></span>
                                    <div class="message"><?= $this->e($comment->message()) ?></div>
                                    <div class="date"><?= $comment->date('hrdi') ?> ago</div>

                                    <div class="moderation">
                                        <label for="comment-delete-<?= $id ?>" title="delete comment" class="delete">
                                            <i class="fa fa-trash-o"></i>
                                        </label>
                                        <input type="radio" name="<?= $id ?>" value="-1" id="comment-delete-<?= $id ?>" class="delete">
                                        <input type="radio" name="<?= $id ?>" value="0" <?= $comment->approved() ? '' : 'checked' ?> class="neutral">
                                        <input type="radio" name="<?= $id ?>" value="1" <?= $comment->approved() ? 'checked' : '' ?> id="comment-approve-<?= $id ?>" class="approve">
                                        <label for="comment-approve-<?= $id ?>" title="approve comment" class="approve">
                                            <i class="fa fa-thumbs-o-up"></i>
                                        </label>
                                    </div>
                                </li>
                            <?php endforeach ?>
                        </ul>     
                    </form>
                <?php endif ?>
            </details>

            <details id="help" <?= $workspace->collapsemenu() ? '' : 'open' ?>>
                <summary>Help</summary>
                <?php $this->insert('edithelp') ?>
            </details>
        </div>

    </div>

</aside>
