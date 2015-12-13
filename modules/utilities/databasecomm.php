<?php
	require("database.php");

	function insertUser($username, $hash){
		$db = new Database();

		$username = $db->link->real_escape_string($username);
		$hash = $db->link->real_escape_string($hash);


		$db->doSQL("INSERT INTO `Users`(`userID`,`hash`) VALUES ('$username','$hash')");

		$result = $db->getRecord();
		if(empty($result))
				return false;
			else
				return true;

		$db->closeConnection();
	}

	function getUser($username){
		$db = new Database();

		$username = $db->link->real_escape_string($username);

		$db->doSQL("SELECT * FROM `Users` WHERE `userID` = '$username'");

		$result = $db->getRecord();
		if(empty($result))
				return false;
			else
				return true;

		$db->closeConnection();
	}

	function updateHash($username, $hash){
		$db = new Database();

		$username = $db->link->real_escape_string($username);
		$hash = $db->link->real_escape_string($hash);


		$db->doSQL("UPDATE `Users` SET `hash` = '$hash' WHERE `userID` = '$username'");

		$result = $db->getRecord();
		if(empty($result))
				return false;
			else
				return true;

		$db->closeConnection();
	}

	function getHash($username){
		$db = new Database();
		$db->doSQL("SELECT * FROM `Users` WHERE `userID` = '$username'");
		$result = $db->getRecord();
		$db->closeConnection();
		return mysqli_fetch_array($result)['hash'];
	}

	function insertToken($username, $token, $description){
		$db = new Database();

		$username = $db->link->real_escape_string($username);

		$db->doSQL("INSERT INTO `Token`(`userID`,`token`, `description`) VALUES ('$username','$token','$description')");

		$result = $db->getRecord();
		if(empty($result))
				return false;
			else
				return true;

		$db->closeConnection();
	}
?>