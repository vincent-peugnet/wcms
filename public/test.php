<?php


require('../w/class/art2.php');
require('../w/class/render.php');

$render = new Render(['head' => 'ccccccc', 'body' => 'vvvvvvvvvvvv']);

$render2 = ['head' => 'nnnnnnnnnn', 'body' => 'bbbbbbbbbbbbbbbbbbbbbbb'];

$render3 = json_decode(json_encode($render2));

var_dump($render3);

var_dump($render);

$art = new Art2(['id' => 'rr']);
$art->reset();
$art->hydrate((['description' => 'fdsfs', 'secure' => 0, 'render' => $render2]));

var_dump($art);

$artencoded = json_encode($art);

var_dump($artencoded);

class Person
{
    public $id;
    public $name;
    public $table = ['coucou', 'lol', 'chouette'];
    public $date;

    public function __construct(array $data) 
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->date = new DateTimeImmutable();
    }
}

$person = new Person(array('id' => 1, 'name' => 'Amir'));
$jsonperson =  json_encode($person);

var_dump($jsonperson);

var_dump(json_decode($jsonperson));



?>