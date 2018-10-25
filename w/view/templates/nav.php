<div class="menu">
    <?= $this->e($level) ?>
    <div id="dropmenu">

    <a class="button" href="?">home</a>

        <!-- id if level == 0 -->
        <form action="./?action=login<?= isset($this->e($id)) ? '&id=' . $this->e($id) : '' ?>" method="post">
        <input type="password" name="pass" id="loginpass" placeholder="password">
        <input type="submit" value="login">
        </form>
            

        <!-- id if level > 0 -->    
        <form action="./?action=logout<?= isset($this->e($id)) ? '&id=' . $this->e($id) : '' ?>" method="post">
        <input type="submit" value="logout">
        </form>


        <!-- display / edit -->    

        
        if ($app->session() >= $app::EDITOR && isset($_GET['id']) && $app->exist($_GET['id'])) {
            if (isset($_GET['edit']) && $_GET['edit'] == 1) {
                echo '<a class="button" href="?id=' . $_GET['id'] . '" target="_blank">display</a>';
            } else {
                echo '<a class="button" href="?id=' . $_GET['id'] . '&edit=1" >edit</a>';
            }
        }
        if ($app->session() >= $app::EDITOR) {
            echo '<a class="button" href="?aff=media" >Media</a>';
            echo '<a class="button" href="?aff=record" >Record</a>';
            if ($app->session() >= $app::ADMIN) {
                echo '<a class="button" href="?aff=admin" >Admin</a>';
            }
        }




</div>
</dav>