<div id="topbar">
        <form id="delete" action="./" method="get">
        <input type="hidden" name="id" value="<?= $art->id() ?>">
        </form>

    <form action="?id=<?= $art->id() ?>&action=update" method="post" id="update">

    <span>
    <input type="submit" name="action" value="update" accesskey="x" form="update">
    </span>


    <span>
        <input type="submit" name="action" value="delete" form="delete">
    </span>


    <span>
    <a href="?id=<?= $art->id() ?>" target="_blank">👁</a>
    <a href="?id=<?= $art->id() ?>&aff=log" target="_blank">¶</a>
    </span>
    <span id="headid"><?= $art->id() ?></span>

<span>

    <label for="fontsize">Font-size</label>
    <input type="number" name="fontsize" value="<?= Config::fontsize() ?>" id="fontsize">
</span>
</div>