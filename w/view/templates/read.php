<html>



<head>
    <meta charset="utf8" />
    <meta name="description" content="<?= $art->description() ?>" />
    <meta name="viewport" content="width=device-width" />
    <link rel="shortcut icon" href="./media/logo.png" type="image/x-icon">
    <link href="<?= $globalcss ?>" rel="stylesheet" />
    <?= $edit == 0 ? '<link href="' . $globalcss . '" rel="stylesheet" />' : '<link href="./rsc/css/styleedit.css" rel="stylesheet" />' ?>
    <title><?= $edit == 1 ? '✏' : '' ?> <?= $art->title() ?></title>
    <script src="./rsc/js/app.js"></script>
</head>

<?= $html ?>

    

</html>