<aside id="rightbar" class="toggle-panel-container">
    <input id="showeditorrightpanel" name="showeditorrightpanel" value="1" class="toggle-panel-toggle" type="checkbox" <?= $workspace->showeditorrightpanel() === true ? 'checked' : '' ?> form="workspace-form" >
    <label for="showeditorrightpanel" class="toggle-panel-label"><span><i class="fa fa-info"></i></span></label>

    <div class="toggle-panel" id="rightbarpanel">

        <h2>Infos</h2>
    
        <div class="toggle-panel-content flexcol">
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

            <h3>Help</h3>
            <div id="help">
                <?php $this->insert('edithelp') ?>
            </div>           
        </div>

    </div>

</aside>
