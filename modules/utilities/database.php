<?php 
/*
 * Class designed to provide simple communication with a database
 * 
 * @author: Bram Bosch
 */
class Database{
	//Class variables
	public $link;
	private $result;

	/*
	 * Constructor.
	 * Automatically connects to a mysql server specified in config.inc.php and a database.
	 * 
	 * @param $db: (String) Name of the database
	 */
	public function Database(){
		include(__DIR__ . "/config.inc.php");
		//$link = mysql_connect($location, $login, $passsword)
		$this->link = new mysqli($location, $login, $password, $dbName);
		if ($this->link->connect_errno != 0) {
				die("Can't connect to server...");
		}
	}
	
	/*
	 * Execute a query in the current database.
	 * 
	 * @param $query: (String) Query to be executed 
	 */	
	public function doSQL($query){
		$this->result = $this->link->query($query);
		//echo "$query \n";
		//print_r($this->link->error_list);
	}
	
	/*
	 * Get previous query result.
	 * 
	 * @param $result: Result of previous query
	 */		
	public function getRecord(){
		return $this->result;
	}
	
	/*
	 * Returns last generated id.
	 */
	public function getInsertId(){
		return $this->link->insert_id;
	}
	
	/*
	 * Close connection with the database
	 */
	public function closeConnection(){
		if ($this->result != true && $this->result != false)
			$this->result->close();
		$this->link->close();
	}
}?>