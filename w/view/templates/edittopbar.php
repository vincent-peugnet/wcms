<div id="submit">
    <!-- <input type="submit" name="action" value="home" accesskey="w" onclick="document.getElementById('artedit').submit();" form="artedit"> -->
    <input type="submit" name="action" value="update" accesskey="x" onclick="document.getElementById('artedit').submit();" form="artedit">
    <!-- <input type="submit" name="action" value="display" accesskey="c" onclick="document.getElementById('artedit').submit();" form="artedit"> -->
    <!-- <input type="submit" name="action" value="delete" onclick="confirmSubmit(event, 'Delete this article', 'artedit')" form="artedit"> -->
    <a href="?id=<?= $art->id() ?>" target="_blank">👁</a>

    <span id="headid"><?= $art->id() ?></span>

    <label for="fontsize">Font-size</label>
    <input type="number" name="fontsize" value="<?= Config::fontsize() ?>" id="fontsize">
</div>