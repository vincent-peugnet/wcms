<?php


require('../w/class/art2.php');

$art = new Art2(['id' => 'rr']);
$art->reset();
$art->hydrate((['description' => 'fdsfs', 'secure' => 0]));

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