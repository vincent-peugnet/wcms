

<?php

$pattern = "/%%(\w*)%%/";

$string = "bla bla bld dd d  , dfddddddsdsdf fzpapap %q dsq%%c%%dd % dsqqsd   %%   dsqq dsq sd  %%c%%dsssssssssss 			 dsqd            %%coucouhibou%%  fdsf			 fdsf						fdsfsdfsdf   ";
						


preg_match_all($pattern, $string, $out);

foreach ($out[0] as $key => $value) {
	$replace = '^^ÔHYEAH BB££___'.$out[1][$key]. '_____§§§§§§';
	$string = str_replace($value, $replace, $string);
	
}


var_dump($out);

var_dump($string);

?>