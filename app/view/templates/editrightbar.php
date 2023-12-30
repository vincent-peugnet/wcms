<div id="rightbar" class="bar">
    <input
        id="showeditorrightpanel"
        name="showeditorrightpanel"
        value="1"
        class="toggle"
        type="checkbox"
        form="workspace-form"
        <?= $workspace->showeditorrightpanel() === true ? 'checked' : '' ?>
    >
    <label for="showeditorrightpanel" class="toogle">◧</label>
    <div id="rightbarpanel" class="panel">
    

        <details id="lastedited" open>
            <summary>Last edited</summary>
        <ul>
        <?php
        foreach ($lasteditedpagelist as $id) {
            ?>
            <li><a href="<?= $this->upage('pageedit', $id) ?>"><?= $id === $page->id() ? '➤' : '✎' ?> <?= $id ?></a></li>
            <?php
        }

        ?>
        </ul>

        </details>


        <details id="tags" open>
            <summary>Tags</summary>
            <?php
            foreach ($tagpagelist as $tag => $idlist) {
                if(count($idlist) > 1) {
                ?>
                <strong><?= $tag ?></strong>
                <?php

                echo '<ul>';
                foreach ($idlist as $id) {
                    if($id === $page->id()) {
                        echo '<li>➤ '.$id.'</li>';
                    } else {
                    ?>
                    <li><a href="<?= $this->upage('pageedit', $id) ?>">✎ <?= $id ?></a></li>
                    <?php
                    }
                }
                }
                echo '</ul>';
            }

            ?>

        </details>

        <details id="templates" open>
            <summary>Templates</summary>
            <ul>
            
            <?php
            foreach ($templates as $template => $id) {
                if(!empty($id) && !is_bool($id)) {
                    ?>
                    <li><?= $template ?> : <?= $id ?> <a href="<?= $this->upage('pageedit', $id) ?>">✎</a></li>
                    <?php
                }
            }
            
            ?>
            </ul>
            
        </details>


        <h3>Authors</h3>

        <?php
            if($user->level() >= 4) {
                foreach ($editorlist as $editor) {
                    ?>
                    <div class="checkexternal">
                    <input type="checkbox" name="authors[]" id="<?= $editor->id() ?>" value="<?= $editor->id() ?>" form="update" <?= in_array($editor->id(), $page->authors()) ? 'checked' : '' ?>>
                    <label for="<?= $editor->id() ?>" ><?= $editor->id() ?> <?= $editor->level() ?></label>
                    </div>
                    <?php
                }
            }
        ?>



        <h3>Stats</h3>

        <table>
            <tbody>
                <tr>
                    <td>edition:</td>
                    <td><?= $page->editcount() ?></td>
                </tr>
                <tr>
                    <td>display:</td>
                    <td><?= $page->displaycount() ?></td>
                </tr>
                <tr>
                    <td>visit:</td>
                    <td><?= $page->visitcount() ?></td>
                </tr>
            </tbody>
        </table>

    </div>

</div>
