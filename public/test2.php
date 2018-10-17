<?php
session_start();




?>


<html>

<div class="prot">

<?php

require('../w/class/class.w.quickcss.php');

$quick = new Quickcss($_POST);

//$quick->setjson($_SESSION['css']);

$quick->calc();

$quick->form('test2.php');

$_SESSION['css'] = $quick->tojson();


?>

</div>





<style>

form {
    position: fixed;
    right: 0;
    top: 0;
    width: 250px;
    height: 100%;
    overflow: scroll;
    background-color: #d8d8d8;
    border: 1px solid black;

}

.quickinput {
    width: 100%;
    background-color: #755454;
    display: inline-flex;
}

input, select{
    width: -webkit-fill-available;
}


<?= $quick->tocss() ?>

</style>

<body>
<span class="u">BODY</span>
    <section>
    <span class="u">SECTION</span>
    <h1><span class="u">H1</span>Bonjour tout le monde</h1>

    <p><span class="u">P</span>Des bails de oufs qui toueeeett</p>

    <article>
    <span class="u">ARTICLE</span>
        <h2><span class="u">H2</span>YOLO</h2>
        <p><span class="u">P</span> Des bails noirs très noir....</p>
    </article>
    
    
    </section>
</body>

</html>
