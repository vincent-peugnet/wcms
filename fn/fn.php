secure

session

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
?>

