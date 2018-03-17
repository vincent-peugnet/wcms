<html>
<a href="/w/">w</a>
</html>

<?php

try
{
	$bdd = new PDO('mysql:host=localhost;dbname=wcms;charset=utf8', 'root', '');
}
catch(Exception $e)
{
        die('Erreur : '.$e->getMessage());
}

// $reponse = $bdd->query('SELECT nom, age FROM art2 WHERE nom = \'eddie\'');

// while ($donnees = $reponse->fetch())
// {
// 	echo $donnees['nom'] . ' a ' . $donnees['age'] . ' ANS<br />';
// }

// $reponse->closeCursor();

// $req = $bdd->prepare('SELECT nom, age FROM art2 WHERE age = 23 ');
// $req->execute(array($_GET['possesseur'], $_GET['prix_max']));

// echo '<ul>';
// while ($donnees = $req->fetch())
// {
// 	echo '<li>' . $donnees['nom'] . ' (' . $donnees['prix'] . ' EUR)</li>';
// }
// echo '</ul>';

// $req->closeCursor();

$req = $bdd->prepare('SELECT * FROM art WHERE id = :id ');
$req->execute(array('id' => 'articlet'));

echo '<ul>';
while ($donnees = $req->fetch())
{
    echo '<li>' . $donnees['titre'] . ' (' . $donnees['id'] . ' ANS)</li>';
}
echo '</ul>';

$req->closeCursor();



?>