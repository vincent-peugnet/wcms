<?php
$text = 'gfgdfgdfgdsggggggggggggg
dfgdsfgdh
dh

gh
g
hg
fhhhhhhhhhhhhhhhhhhgfghfghfgh

fhfgfgdfgdfgdsggggggggggggg
dfgdsfgdh
dh

gh
g
hg
fhhhhhhhhhhhhhhhhhhgfghfghfgh

fhf<h3 id="titre2laguerre">tiTre2:laguerre</h3>  dqs fdddddddfdfdsfs fdsfdksdfjnsdjkhsf lkfljkfhjldshsf fsdf
fdgfgdfgdfgdsggggggggggggg
dfgdsfgdh
dh

gh
g
hg
fhhhhhhhhhhhhhhhhhhgfghfghfgh
<p>
fhfgfgdfgdfgdsggggggggggggg
dfgdsfgdh</p>
dh</br>dh</br>dh</br>dh</br>dh</br>dh</br>dh</br>dh</br>dh</br>

gh<p>
g
hg</p>
fhhhhhhhhhhhhhhhhhhgfghfghfgh
gh
g
hg
fhhhhhhhhhhhhhhhhhhgfghfghfgh
<p>
fhfgfgdfgdfgdsggggggggggggg
dfgdsfgdh</p>
dh</br>dh</br>dh</br>dh</br>dh</br>dh</br>dh</br>dh</br>dh</br>

gh<p>
g
hg</p>
fhhhhhhhhhhhhhhhhhhgfghfghfgh
gh
<h1 id="yolo">YOLO</h1>
<h2 id="ptout">PROUT</h2>
<h2 id="lol">LOLDELAMORT</h2>
hg
fhhhhhhhhhhhhhhhhhhgfghfghfgh

<p>
fhfgfgdfgdfgdsggggggggggggg
dfgdsfgdh</p>
dh</br>dh</br>dh</br>dh</br>dh</br>dh</br>dh</br>dh</br>dh</br>

gh<p>
g
hg</p>
fhhhhhhhhhhhhhhhhhhgfghfghfgh

fhf
g
hffffgggggggggggggggggggggggggggggggggggggdsf <h3>titre 3- les hommes </h3> la fin';


function sumparser($text)
{
    preg_match_all('#<h([1-6]) id="(\w+)">(.+)</h[1-6]>#iU', $text, $out);

    var_dump($out);

    $sum = [];
    foreach ($out[2] as $key => $value) {
        $sum[$value][$out[1][$key]] = $out[3][$key];
    }

    var_dump($sum);

    $sumstring = '';
    $last = 0;
    foreach ($sum as $title => $list) {
        foreach ($list as $h => $link) {
            if($h > $last) {
                for ($i = 1; $i <= ($h - $last); $i++) {
                    $sumstring .= '<ul>';
                }            
                $sumstring .= '<li><a href="#'.$title.'">'.$link.'</a></li>' ;
            } elseif ($h < $last) {
                for ($i = 1; $i <= ($last - $h); $i++) {
                    $sumstring .= '</ul>';
                }
                $sumstring .= '<li><a href="#'.$title.'">'.$link.'</a></li>' ;            
            } elseif ($h = $last) {
                $sumstring .= '<li><a href="#'.$title.'">'.$link.'</a></li>' ;
            }
            $last = $h;
        }
    }
    for ($i = 1; $i <= ($last); $i++) {
        $sumstring .= '</ul>';
    }
    return $sumstring;
}





echo $sumstring;


echo $text.'</br>';