<?php

class Controllerdb extends Controller
{

    protected $artmanager;
    protected $database;
    protected $artstore;


    public function __construct()
    {
        parent::__construct();


    }




    // if (isset($_POST['actiondb'])) {
    //     $app->setbdd($config);
    
    //     switch ($_POST['actiondb']) {
    
    //         case 'addtable':
    //             if (isset($_POST['tablename'])) {
    //                 $message = Modeldb::addtable($config->dbname(), $_POST['tablename']);
    //                 header('Location: ./?aff=admin&message=' . $message);
    //             }
    //             break;
    
    //         case 'duplicatetable':
    //             $message = Modeldb::tableduplicate($config->dbname(), $_POST['arttable'], $_POST['tablename']);
    //             header('Location: ./?aff=admin&message=' . $message);            
    //             break;
    
    //     }
    // }
}




?>