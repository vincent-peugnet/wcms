<div id="submit">
    <!-- <input type="submit" name="action" value="home" accesskey="w" onclick="document.getElementById('artedit').submit();" form="artedit"> -->
    <input type="submit" name="action" value="update" accesskey="x" form="update">
    <!-- <input type="submit" name="action" value="display" accesskey="c" onclick="document.getElementById('artedit').submit();" form="artedit"> -->
    


    <form id="delete" action="./" method="get">
    <input type="hidden" name="id" value="<?= $art->id() ?>">
    <input type="submit" name="action" value="delete" form="delete">
    </form>


    <a href="?id=<?= $art->id() ?>" target="_blank">👁</a>
    <a href="?id=<?= $art->id() ?>&aff=log" target="_blank">¶</a>
    <span id="headid"><?= $art->id() ?></span>


    <form action="?id=<?= $art->id() ?>&action=update" method="post" id="update">

    <label for="fontsize">Font-size</label>
    <input type="number" name="fontsize" value="<?= Config::fontsize() ?>" id="fontsize">
</div>