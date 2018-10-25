<html>
<head>
    <meta charset="utf8" />
    <meta name="description" content="<?=$this->e($description)?>" />
    <link href="<?=$this->e($id)?>quickcss" rel="stylesheet" />
    <link href="<?=$this->e($id)?>" rel="stylesheet" />
    <title><?=$this->e($title)?></title>

</head>

<script>
<?=$this->e($javascript)?>
</script>

<body>

<header>
<?=$this->e($header)?>
</header>

<nav>
<?=$this->e($nav)?>
</nav>

<aside>
<?=$this->e($aside)?>
</aside>

<section>
<?=$this->e($section)?>
</section>

<footer>
<?=$this->e($footer)?>
</footer>

</body>
</html>