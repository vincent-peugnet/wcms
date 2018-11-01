<?php

class Controllerdb extends Controller
{

    protected $artmanager;
    protected $database;
    protected $artstore;


    public function __construct()
    {
        parent::__construct();
        $this->artmanager = new Modelart();

    }



    public function fetch()
    {
        $datas = $this->artstore->fetch();
        return $datas;
    }

    public function desktop()
    {
        

        $this->dbinit();
        var_dump( $this->fetch());


    }

    public function add()
    {
        $user = $usersDB->where( 'name', '=', 'Joshua Edwards' )->fetch();
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