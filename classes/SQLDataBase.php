<?php
//////////////////////////////////////////
//					//
//	SQLDataBase.php		//
//					//
//	Class to create easy to use	//
//	interface with the database	//
//					//
//	By David Slater			//
//	June 2009			//
//					//
//////////////////////////////////////////


class SQLDataBase {

	////////////////Private Variables//////////

	private $link; //mysql database link
	private $host;	//hostname for the database
	private $database; //database name
	private $username; //username to connect to the database
	private $password; //password of the username

	////////////////Public Functions///////////

	public function __construct($host,$database,$username,$password) {
		$this->open($host,$database,$username,$password);
	}
	public function __destruct() {
		 

	}

	//open()
	//$host - hostname
	//$database - name of the database
	//$username - username to connect to the database with
	//$password - password of the username
	//opens a connect to the database
	public function open($host,$database,$username,$password) {
		//Connects to database.

                $this->link = mysqli_connect($host,$username,$password) or die("Unable to connect to database");
		@mysqli_select_db($this->link, $database) or die("Unable to select database " . $database);
		$this->host = $host;
		$this->database = $database;
		$this->username = $username;
		$this->password = $password;


	}

	//close()
	//closes database connection
	public function close() {
		mysqli_close($this->link);
	}

	//insert_query()
	//$sql - sql string to run on the database
	//returns the id number of the new record, 0 if it fails
	public function insertQuery($sql) {

                if (mysqli_query($this->link, $sql)) {

			return mysqli_insert_id($this->link);
		}
		else {

			return 0;
		}

	}

	//non_select_query()
	//$sql - sql string to run on the database
	//For update and delete queries
	//returns true on success, false otherwise
	public function nonSelectQuery($sql) {

            set_time_limit ( 3000 );
                $result = mysqli_query($this->link, $sql);
	}

	//query()
	//$sql - sql string to run on the database
	//Used for SELECT queries
	//returns an associative array of the select query results.
	public function query($sql) {

                $result = mysqli_query($this->link, $sql);

		return $this->mysqlToArray($result);
	}

	//count_query()
	//$sql - sql string to run on the database
	//Used for SELECT queries
	//returns number of rows in result
	public function countQuery($sql) {
		//$result = mysqli_query($sql,$this->link);
                $result = mysqli_query($this->link, $sql);
		return mysqli_num_rows($result);
	}

	//single_query()
	//$sql - sql string to run on the database
	//Used for SELECT queries
	//returns single result value in first row
	public function singleQuery($sql) {

                $result = mysqli_query($this->link, $sql);

		$row = mysqli_fetch_array($result,MYSQL_ASSOC);

                if($row != null) {
			foreach($row as $key=>$data) {
				//there should be only one
                                return $data;
			}
                }
			
		
		//return @$dataArray;
	}

	//getLink
	//returns the mysql resource link
	public function getLink() {
		return $this->link;
	}

	public function fetchAssoc($sql)
	{
		//$result = mysqli_query($sql);
            $result = mysqli_query($this->link, $sql);
		$rows = mysqli_fetch_assoc($result);
		return $rows;
	}
	/////////////////Private Functions///////////////////

	//mysqlToArray
	//$mysqlResult - a mysql result
	//returns an associative array of the mysql result.
	private function mysqlToArray($mysqlResult) {
		$dataArray;
		$i =0;
		while($row = mysqli_fetch_array($mysqlResult,MYSQL_BOTH)){
                
			foreach($row as $key=>$data) {
				$dataArray[$i][$key] = $data;
			}
			$i++;
		}
		return @$dataArray;
	}

	public function GetSqlUsername()
	{
		return $this->username;
	}

	public function GetSqlPassword()
	{
		return $this->password;
	}

	public function GetDatabase()
	{
		return $this->database;
	}
        
        public function get_query_result($query_string, $query_array) {
            $statement = $this->get_link()->prepare($query_string, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $statement->execute($query_array);
            $result = $statement->fetchAll();
            return $result;

        }


        public function get_update_result($query_string, $query_params) {
            // Update queries should probably only update one record. This will ensure 
            // only one record gets updated in case of a malformed query.
            $query_string .= " LIMIT 1"; 
            $statement = $this->get_link()->prepare($query_string, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $result = $statement->execute($query_params);
            return $result;
        }

        public function get_insert_result($query_string, $query_array) {

            $statement = $this->get_link()->prepare($query_string, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $stmt = $statement->execute($query_array);
            $result =  $this->get_link()->lastInsertId();
            return $result;
        }

}

?>
