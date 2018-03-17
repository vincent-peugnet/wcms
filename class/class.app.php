<?php
class App
{
	protected $bdd;

	public function __construct($config)
	{
		$host = $config['host'];
		$dbname = $config['dbname'];
		$user = $config['user'];
		$password = $config['password'];
		try {
			$this->bdd = new PDO('mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8', $user, $password);
		} catch (Exeption $e) {
			die('Erreur : ' . $e->getMessage());
		}
	}

	public function getBdd()
	{
		return $this->bdd;
	}

	public function createfrombdd($id, $bdd)
	{

	}

}
?>