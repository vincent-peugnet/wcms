<?php

// _____________________________________________________ R E Q U I R E ________________________________________________________________


$config = require('../../config.php');



require('../../fn/fn.php');
require('../../class/class.art.php');
require('../../class/class.media.php');
require('../../class/class.app.php');
require('../../class/class.aff.php');
session();
if (!isset($_SESSION['level'])) {
	$level = 0;
} else {
	$level = $_SESSION['level'];
}
$app = new App($config);
$aff = new Aff($level);



// ______________________________________________________ H E A D _____________________________________________________________
$titre = 'home';
$aff->head($titre, 'm');

// _____________________________________________________ A L E R T _______________________________________________________________ 

if (isset($_GET['message'])) {
	echo '<h4>' . $_GET['message'] . '</h4>';
}



// ____________________________________________________ A C T I O N _______________________________________________________________ 


if (isset($_POST['action'])) {
	switch ($_POST['action']) {
		case 'addmedia':
			$app->addmedia($_FILES, 2 ** 30, $_POST['id']);
			break;

		case 'login':
			$_SESSION['level'] = $app->login($_POST['pass']);
			if (isset($_GET['id'])) {
				header('Location: ?id=' . $_GET['id']);
			} else {
				header('Location: ?');
			}
			break;

		case 'logout':
			$_SESSION['level'] = $app->logout();
			if (isset($_GET['id'])) {
				header('Location: ?id=' . $_GET['id']);
			} else {
				header('Location: ?');
			}
			break;
	}
}

// ______________________________________________________ B O D Y _______________________________________________________________ 



echo '<body>';
$aff->nav($app);
$aff->addmedia();


echo '<details open>';
echo '<summary>Media List</summary>';
echo '<h1>Media</h1>';

echo '<section class="gest">';

$dir = "../media/";

echo '<form action="" method="post">';

echo '<div class="grid">';

if ($handle = opendir($dir)) {
	while (false !== ($entry = readdir($handle))) {
		if ($entry != "." && $entry != "..") {
			$fileinfo = pathinfo($entry);

			$filepath = $dir . $fileinfo['filename'] . '.' . $fileinfo['extension'];

			list($width, $height, $type, $attr) = getimagesize($filepath);
			$filesize = filesize($filepath);

			echo '<div class="little">';


			?>
			<label for="<?= $entry ?>"><?= $entry ?></label>
			<input type="hidden" name="" value="">
			<input type="checkbox" id="<?= $entry ?>" name="<?= $entry ?>" value="1">

			<?php

		echo '<img class="thumbnail" src="' . $filepath . '" alt="' . $fileinfo['filename'] . '">';

		echo '<span class="infobulle">';
		echo 'width = ' . $width . ' px';
		echo '<br/>';
		echo 'height = ' . $height . ' px';
		echo '<br/>';
		echo 'filesize = ' . readablesize($filesize);
		echo '<br/>';

		echo '<input type="text" value="![' . $fileinfo['filename'] . '](/' . $entry . ')">';
		echo '<br/>';


		echo '<img src="' . $filepath . '" alt="' . $fileinfo['filename'] . '">';
		echo '</span>';


		echo '</div>';
	}
}
closedir($handle);
}

echo '</div>';

?>
<select name="action" id="">
	<option value="">compress /2</option>
	<option value="">downscale /2</option>
	<option value="">upscale *2</option>
</select>
<input type="submit" value="edit">
<input type="submit" value="delete">
</form>
</div>


<?php


echo '</section>';
echo '</details>';

var_dump($app->getlistermedia($dir));

echo '</body>';


?>



