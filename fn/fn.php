<?php
function bddconnect($host, $bdname, $user, $password)
{
	try {
		$bdd = new PDO('mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8', $user, $password);
	} catch (Exeption $e) {
		die('Erreur : ' . $e->getMessage());
	}
	return $bdd;
}

function session()
{
	session_start();
}

function secure()
{
	if (!isset($_SESSION['id'])) {
		header("location: /");
	}
}

function head($title)
{
	?>
	<head>
		<meta charset="utf8" />
		<meta name="viexport" content="width=device-width" />
		<link href="/css/style.css" rel="stylesheet" />
		<title><?= $title ?></title>
	</head>
	<?php

}

function search($haystack, $debut, $fin)
{
	$list = [];

	$indexdebut = strpos($haystack, $debut);
	if ($indexdebut !== false) {
		$indexdebut += strlen($debut);
		$indexfin = strpos($haystack, $fin, $indexdebut);
		if ($indexfin !== false) {
			array_push($list, substr($haystack, $indexdebut, $indexfin - $indexdebut));
			$haystack = substr($haystack, $indexfin);
			$list = array_merge($list, search($haystack, $debut, $fin));
		}
	}
	return $list;

}

?>

